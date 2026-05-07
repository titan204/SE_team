<?php

interface BillableInterface
{
    public function getBillingSubject();

    public function setBillingSubject($subject);

    public function getInvoice();

    public function getInvoiceItems(): array;
}

