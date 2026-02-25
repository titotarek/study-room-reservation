<?php

namespace App\Repositories;

interface IUserRepository
{
    public function findByEmail(string $email);

    public function verifyUser(string $email, string $password);

    public function findById(int $id);
}
