<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function getUserById ($id) {

        return $this->userRepository->findUserById($id);

    }
}