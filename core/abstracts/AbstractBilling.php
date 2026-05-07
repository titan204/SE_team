<?php

abstract class AbstractBilling extends AbstractModel implements BillableInterface
{
    private $invoice;
    private string $billingSubject = '';

    public function __construct($db = null, $invoice = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->invoice = $invoice ?: new Invoice();
    }

    public function getBillingSubject()
    {
        return $this->billingSubject;
    }

    public function setBillingSubject($subject)
    {
        $this->billingSubject = trim((string) $subject);
        return $this;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function getInvoiceItems(): array
    {
        return $this->invoice->getItems();
    }

    protected function addInvoiceLine(string $description, float $amount, int $quantity = 1, string $type = 'manual'): void
    {
        $this->invoice->addItem(new InvoiceItem($description, $amount, $quantity, $type));
    }
}

