<?php

class InvoiceItem
{
    private string $description;
    private float $amount;
    private int $quantity;
    private string $type;

    public function __construct(string $description, float $amount, int $quantity = 1, string $type = 'manual')
    {
        $this->description = $description;
        $this->amount = $amount;
        $this->quantity = max(1, $quantity);
        $this->type = $type;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLineTotal(): float
    {
        return round($this->amount * $this->quantity, 2);
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'quantity' => $this->quantity,
            'type' => $this->type,
            'line_total' => $this->getLineTotal(),
        ];
    }
}

