<?php

namespace App\Controller;


namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/villes/{villeId}/lieux', name: 'lieux_list')]
    public function list(EntityManagerInterface $em, int $villeId): Response
    {
        $lieux = $em->getRepository(Lieu::class)->findBy(['idVille' => $villeId]);
        $villeId = $em->getRepository(Ville::class)->findBy(['idVille'=> $villeId]);

        return $this->render('lieu/list.html.twig', [
            'lieux' => $lieux,
            'villeId' => $villeId
        ]);
    }



    #[Route('/villes/{villeId}/lieux/new', name: 'lieux_new')]
    public function newLieux(int $villeId, Request $request, EntityManagerInterface $em): Response
    {
        $ville = $em->getRepository(Ville::class)->find($villeId);
        if (!$ville) {
            throw $this->createNotFoundException('Ville not found');
        }

        $lieu = new Lieu();
        $lieu->setIdVille($ville); // فرض بر اینه که متد setter داری

        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lieu);
            $em->flush();
            return $this->redirectToRoute('lieux_list', ['villeId' => $ville->getId()]);
        }

        return $this->render('lieux/new.html.twig', [
            'form' => $form->createView(),
            'ville' => $ville
        ]);
    }




}

