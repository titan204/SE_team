<?php

use PHPUnit\Framework\TestCase;

class roomStoreTest extends TestCase
{
    private $room;
    private $db;

    protected function setUp(): void
    {
        $this->db = new mysqli("localhost", "root", "", "hotel_management");

        if ($this->db->connect_error) {
            throw new Exception("DB Connection Failed: " . $this->db->connect_error);
        }

        // 🔥 Start transaction for isolation
        $this->db->begin_transaction();

        $this->room = new Room($this->db);
    }

    // EP-RM-01
    public function testCreateRoomWithValidRoomNumber()
    {
        $data = [
            'room_type_id' => 1,
            'room_number'  => '1001',
            'floor'        => 1,
            'notes'        => 'Test room'
        ];

        $result = $this->room->create($data);

        $this->assertIsNumeric($result);
    }

    // EP-RM-02
    public function testCreateRoomWithEmptyRoomNumber()
    {
        $this->expectException(Exception::class);

        $data = [
            'room_type_id' => 1,
            'room_number'  => '',
            'floor'        => 1
        ];

        $this->room->create($data);
    }

    // EP-RM-03
    public function testValidRoomType()
    {
        $data = [
            'room_type_id' => 1,
            'room_number'  => '1002',
            'floor'        => 1
        ];

        $result = $this->room->create($data);

        $this->assertTrue($result > 0);
    }

    // EP-RM-04
    public function testEmptyRoomType()
    {
        $this->expectException(Exception::class);

        $data = [
            'room_type_id' => null,
            'room_number'  => '103',
            'floor'        => 1
        ];

        $this->room->create($data);
    }
public function testValidStatusTransition()
{
    $roomId = $this->room->create([
        'room_type_id' => 1,
        'room_number'  => '2001',
        'floor'        => 2
    ]);

    // available → occupied (ONLY allowed path)
    $result = $this->room->updateStatus($roomId, 'occupied');

    $this->assertTrue($result);
}
    // EP-RM-08
    public function testInvalidStatusTransition()
    {
        $this->expectException(Exception::class);

        $roomId = $this->room->create([
            'room_type_id' => 1,
            'room_number'  => '2002',
            'floor'        => 2
        ]);

        $this->room->updateStatus($roomId, 'dirty');
    }

    // EP-RM-09
    public function testDeleteRoomWithoutReservations()
    {
        $roomId = $this->room->create([
            'room_type_id' => 1,
            'room_number'  => '3001',
            'floor'        => 3
        ]);

        $result = $this->room->delete($roomId);

        $this->assertTrue($result);
    }

    // EP-RM-10
    public function testDeleteRoomWithActiveReservations()
    {
        $this->expectException(Exception::class);

        $roomId = $this->room->create([
            'room_type_id' => 1,
            'room_number'  => '3002',
            'floor'        => 3
        ]);

        $this->db->query("
            INSERT INTO reservations (room_id, status)
            VALUES ($roomId, 'active')
        ");

        $this->room->delete($roomId);
    }

    protected function tearDown(): void
    {
        // 🔥 rollback everything (no DB pollution)
        $this->db->rollback();
        $this->db->close();
    }
}