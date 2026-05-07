<?php

class RoomFeatures
{
    private array $amenities = [];
    private array $accessibilityFlags = [];

    public function addAmenity(string $amenity): self
    {
        $amenity = trim($amenity);
        if ($amenity !== '' && !in_array($amenity, $this->amenities, true)) {
            $this->amenities[] = $amenity;
        }

        return $this;
    }

    public function getAmenities(): array
    {
        return $this->amenities;
    }

    public function addAccessibilityFlag(string $flag): self
    {
        $flag = trim($flag);
        if ($flag !== '' && !in_array($flag, $this->accessibilityFlags, true)) {
            $this->accessibilityFlags[] = $flag;
        }

        return $this;
    }

    public function getAccessibilityFlags(): array
    {
        return $this->accessibilityFlags;
    }

    public function toArray(): array
    {
        return [
            'amenities' => $this->amenities,
            'accessibility_flags' => $this->accessibilityFlags,
        ];
    }
}

