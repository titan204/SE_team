<?php

class ProfileData
{
    private string $name = '';
    private string $email = '';
    private string $phone = '';

    public function fill(array $data): self
    {
        if (array_key_exists('name', $data)) {
            $this->setName((string) $data['name']);
        }

        if (array_key_exists('email', $data)) {
            $this->setEmail((string) $data['email']);
        }

        if (array_key_exists('phone', $data)) {
            $this->setPhone((string) $data['phone']);
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = trim($name);
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = trim($email);
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = trim($phone);
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}

