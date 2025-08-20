<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\UserImportType;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'admin_')]
final class AdminController extends AbstractController
{
    #[Route('/userImport', name: 'userImport', methods: ['GET', 'POST'])]
    public function import(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['csv_file']->getData();

            if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                $header = fgetcsv($handle, 1000, ',');

                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $row = array_combine($header, $data);

                    $user = new User();
                    $user->setPrenom($row['prenom']);
                    $user->setNom($row['nom']);
                    $user->setEmail($row['email']);
                    $user->setRoles([$row['role']]);
                    $hashedPassword = $passwordHasher->hashPassword($user, $row['mot_de_passe']);
                    $user->setPassword($hashedPassword);

                    $em->persist($user);
                }

                fclose($handle);
                $em->flush();

                $this->addFlash('success', 'Importation terminée avec succès !');
            }
        }

        return $this->render('admin/user_import.html.twig', [
            'UserImportForm' => $form->createView(),
        ]);
    }
}
