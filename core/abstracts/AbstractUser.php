<?php

abstract class AbstractUser extends AbstractModel
{
    private $profileData;

    public function __construct($db = null, $profileData = null, array $aggregates = [])
    {
        parent::__construct($db, $aggregates);
        $this->profileData = $profileData ?: new ProfileData();
    }

    public function getProfileData()
    {
        return $this->profileData;
    }

    public function getName()
    {
        return $this->getAttribute('name', $this->profileData->getName());
    }

    public function setName($name)
    {
        $this->profileData->setName((string) $name);
        return $this->setAttribute('name', $name);
    }

    public function getEmail()
    {
        return $this->getAttribute('email', $this->profileData->getEmail());
    }

    public function setEmail($email)
    {
        $this->profileData->setEmail((string) $email);
        return $this->setAttribute('email', $email);
    }

    protected function hydrateProfileData(array $data): void
    {
        $this->profileData->fill($data);
    }
}

