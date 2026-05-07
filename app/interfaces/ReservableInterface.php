<?php

interface ReservableInterface
{
    public function confirm($id);

    public function checkIn($id);

    public function checkOut($id);

    public function cancel($id);
}

