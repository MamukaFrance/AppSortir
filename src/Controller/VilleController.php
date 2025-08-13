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

class VilleController extends AbstractController
{
    #[Route('/villes', name: 'villes_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $villes = $em->getRepository(Ville::class)->findAll();

        return $this->render('ville/list.html.twig', [
            'villes' => $villes
        ]);
    }

    #[Route('/villes/new', name: 'villes_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ville);
            $em->flush();

            return $this->redirectToRoute('villes_list');
        }

        return $this->render('ville/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // src/Controller/LieuController.php

    #[Route('/ville/{villeId}/lieux', name: 'lieux_list')]
    public function listLieux(EntityManagerInterface $em, int $villeId, Request $request): Response
    {
        // پیدا کردن Ville
        $ville = $em->getRepository(Ville::class)->find($villeId);
        if (!$ville) {
            throw $this->createNotFoundException('Ville not found.');
        }

        // گرفتن لیست Lieu بر اساس idVille
        $lieux = $em->getRepository(Lieu::class)->findBy(['idVille' => $ville]);

        // ساخت فرم برای اضافه کردن Lieu جدید
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ست کردن شهر روی Lieu
            $lieu->setIdVille($ville);

            $em->persist($lieu);
            $em->flush();

            return $this->redirectToRoute('lieux_list', ['villeId' => $villeId]);
        }

        return $this->render('lieux/list.html.twig', [
            'ville' => $ville,
            'lieux' => $lieux,
            'form' => $form->createView(),
        ]);
    }


}
