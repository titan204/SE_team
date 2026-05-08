<?php

abstract class AbstractReservation extends AbstractModel implements ReservableInterface
{
    private $reservationDetails;

    public function __construct($db = null, $reservationDetails = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->reservationDetails = $reservationDetails ?: new ReservationDetails();
    }

    public function getReservationDetails()
    {
        return $this->reservationDetails;
    }

    protected function hydrateReservationDetails(array $data)
    {
        $this->reservationDetails->fill($data);
        return $this->reservationDetails;
    }
}

