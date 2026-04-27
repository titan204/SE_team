<?php
// ============================================================
//  ExternalService Model — Guest-facing external services
//  Table: external_services
// ============================================================

class ExternalService extends Model
{
    public function all()
    {
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM external_services ORDER BY service_type ASC, name ASC"
        );

        if (!$result) {
            die('Query Failed: ' . mysqli_error($this->db));
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function find($id)
    {
        $id = (int) $id;
        $result = mysqli_query(
            $this->db,
            "SELECT * FROM external_services WHERE id = $id LIMIT 1"
        );

        if (!$result) {
            die('Query Failed: ' . mysqli_error($this->db));
        }

        return mysqli_fetch_assoc($result);
    }
}
