<?php

interface AuthenticatableInterface
{
    public function findByEmail($email);

    public function hashPassword($password);

    public function authenticate($email, $password);
}

