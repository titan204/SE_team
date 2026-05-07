<?php

class Invoice
{
    private array $items = [];
    private float $taxAmount = 0.0;
    private float $discountAmount = 0.0;

    public function addItem(InvoiceItem $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function clearItems(): self
    {
        $this->items = [];
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getSubtotal(): float
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += $item->getLineTotal();
        }

        return round($subtotal, 2);
    }

    public function setTaxAmount(float $taxAmount): self
    {
        $this->taxAmount = max(0.0, $taxAmount);
        return $this;
    }

    public function getTaxAmount(): float
    {
        return $this->taxAmount;
    }

    public function setDiscountAmount(float $discountAmount): self
    {
        $this->discountAmount = max(0.0, $discountAmount);
        return $this;
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    public function getGrandTotal(): float
    {
        return round(max(0.0, $this->getSubtotal() + $this->taxAmount - $this->discountAmount), 2);
    }
}

