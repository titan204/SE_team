<?php

class ReservationDetails
{
    private int $guestId = 0;
    private int $roomId = 0;
    private string $checkInDate = '';
    private string $checkOutDate = '';
    private int $adults = 1;
    private int $children = 0;
    private string $specialRequests = '';
    private float $depositAmount = 0.0;
    private float $totalPrice = 0.0;

    public function fill(array $data): self
    {
        $this->guestId = (int) ($data['guest_id'] ?? $this->guestId);
        $this->roomId = (int) ($data['room_id'] ?? $this->roomId);
        $this->checkInDate = (string) ($data['check_in_date'] ?? $this->checkInDate);
        $this->checkOutDate = (string) ($data['check_out_date'] ?? $this->checkOutDate);
        $this->adults = max(1, (int) ($data['adults'] ?? $this->adults));
        $this->children = max(0, (int) ($data['children'] ?? $this->children));
        $this->specialRequests = trim((string) ($data['special_requests'] ?? $this->specialRequests));
        $this->depositAmount = max(0.0, (float) ($data['deposit_amount'] ?? $this->depositAmount));
        $this->totalPrice = max(0.0, (float) ($data['total_price'] ?? $this->totalPrice));
        return $this;
    }

    public function getGuestId(): int
    {
        return $this->guestId;
    }

    public function getRoomId(): int
    {
        return $this->roomId;
    }

    public function getCheckInDate(): string
    {
        return $this->checkInDate;
    }

    public function getCheckOutDate(): string
    {
        return $this->checkOutDate;
    }

    public function getAdults(): int
    {
        return $this->adults;
    }

    public function getChildren(): int
    {
        return $this->children;
    }

    public function getSpecialRequests(): string
    {
        return $this->specialRequests;
    }

    public function getDepositAmount(): float
    {
        return $this->depositAmount;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function toArray(): array
    {
        return [
            'guest_id' => $this->guestId,
            'room_id' => $this->roomId,
            'check_in_date' => $this->checkInDate,
            'check_out_date' => $this->checkOutDate,
            'adults' => $this->adults,
            'children' => $this->children,
            'special_requests' => $this->specialRequests,
            'deposit_amount' => $this->depositAmount,
            'total_price' => $this->totalPrice,
        ];
    }
}

