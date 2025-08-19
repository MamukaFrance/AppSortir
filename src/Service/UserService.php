<?php

namespace App\Service;

use App\Controller\SecurityController;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private Security $security,
        private FormFactoryInterface $form,
        private string $photosDirectory) {
    }


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

    public function edit(Request $request)
    {
        $user = $this->security->getUser();
        $form = $this->form->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setNom($user->getNom());
            $user->setPrenom($user->getPrenom());
            $user->setEmail($user->getEmail());
            $user->setTelephone($user->getTelephone());

            // Photo de profil
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $extension = $photoFile->guessExtension();
                $nouveauNom = $user->getId() . '.' . $extension;

                try {
                    $photoFile->move(
                        $this->photosDirectory,
                        $nouveauNom
                    );
                } catch (FileException $e) {
                    throw new \Exception('Erreur lors de l\'upload de la photo.');
                }
                $user->setPhoto($nouveauNom);
            }
            $this->userRepository->save($user);
            return ['success' => true];
        }
        return ['form' => $form, 'user' => $user];
    }
}