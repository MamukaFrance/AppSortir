<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function getUserById ($id) {

        return $this->userRepository->findUserById($id);

    }
    public function delete(int $id)
    {
        $user = $this->userRepository->find($id);
        $this->userRepository->deleteUser($user);
    }

    public function desactive(int $id)
    {
        $user = $this->userRepository->find($id);
        $user->setActif(false);
        $this->userRepository->save($user);
    }

    public function list()
    {
        return $this->userRepository->findAll();
    }

}