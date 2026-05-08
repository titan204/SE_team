<?php

interface AggregatesModelsInterface
{
    public function aggregateNames(): array;

    public function getAggregate(string $name);

    public function hasAggregate(string $name): bool;
}

