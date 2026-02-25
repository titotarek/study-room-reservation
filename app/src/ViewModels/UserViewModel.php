<?php

namespace App\ViewModels;

use App\Models\User;

class UserViewModel
{
    public int $id;
    public string $name;
    public string $email;
    public string $role;

    public function __construct(User|array $user)
    {
        $data = $user instanceof User ? get_object_vars($user) : $user;

        $this->id    = (int) ($data['id'] ?? 0);
        $this->name  = (string) ($data['name'] ?? '');
        $this->email = (string) ($data['email'] ?? '');
        $this->role  = (string) ($data['role'] ?? 'student');
    }
}
