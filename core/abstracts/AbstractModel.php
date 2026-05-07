<?php

abstract class AbstractModel extends Model implements AggregatesModelsInterface
{
    private array $attributes = [];
    private array $aggregateTypes = [];
    private array $aggregates = [];

    public function __construct($db = null, array $aggregates = [])
    {
        parent::__construct($db);

        foreach ($aggregates as $name => $aggregate) {
            if (is_string($aggregate)) {
                $this->registerAggregate($name, $aggregate);
            } else {
                $this->setAggregate($name, $aggregate);
            }
        }
    }

    public function __get(string $name)
    {
        $property = $this->declaredProperty($name);
        if ($property !== null) {
            return $property->getValue($this);
        }

        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $property = $this->declaredProperty($name);
        if ($property !== null) {
            $property->setValue($this, $value);
            return;
        }

        $this->attributes[$name] = $value;
    }

    public function getAttribute(string $name, $default = null)
    {
        $property = $this->declaredProperty($name);
        if ($property !== null) {
            return $property->getValue($this);
        }

        return $this->attributes[$name] ?? $default;
    }

    public function setAttribute(string $name, $value): self
    {
        $this->__set($name, $value);
        return $this;
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute((string) $name, $value);
        }

        return $this;
    }

    public function aggregateNames(): array
    {
        return array_values(array_unique(array_merge(
            array_keys($this->aggregateTypes),
            array_keys($this->aggregates)
        )));
    }

    public function hasAggregate(string $name): bool
    {
        return isset($this->aggregateTypes[$name]) || isset($this->aggregates[$name]);
    }

    public function getAggregate(string $name)
    {
        if (isset($this->aggregates[$name])) {
            return $this->aggregates[$name];
        }

        if (!isset($this->aggregateTypes[$name])) {
            return null;
        }

        $className = $this->aggregateTypes[$name];
        if (!class_exists($className)) {
            return null;
        }

        $this->aggregates[$name] = new $className($this->db);
        return $this->aggregates[$name];
    }

    protected function registerAggregate(string $name, string $className): void
    {
        $this->aggregateTypes[$name] = $className;
    }

    protected function setAggregate(string $name, $aggregate): void
    {
        $this->aggregates[$name] = $aggregate;
        if (is_object($aggregate)) {
            $this->aggregateTypes[$name] = get_class($aggregate);
        }
    }

    protected function escape($value): string
    {
        return mysqli_real_escape_string($this->db, (string) $value);
    }

    private function declaredProperty(string $name)
    {
        $reflection = new ReflectionObject($this);

        while ($reflection) {
            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                $property->setAccessible(true);
                return $property;
            }

            $reflection = $reflection->getParentClass();
        }

        return null;
    }
}
