<?php

class Payment extends AbstractBilling
{
    protected $id;
    protected $folio_id;
    protected $amount;
    protected $method;
    protected $reference;
    protected $processed_by;
    protected $processed_at;

    public function __construct($db = null, $invoice = null, array $aggregates = [])
    {
        parent::__construct($db, $invoice, $aggregates);
        $this->setBillingSubject('folio_payment');
        $this->registerAggregate('folio', Folio::class);
    }

}
