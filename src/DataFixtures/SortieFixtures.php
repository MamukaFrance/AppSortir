<?php

namespace App\DataFixtures;

use App\Entity\Site;
use App\Entity\Ville;
use App\Entity\Lieu;
use App\Entity\Etat;
use App\Entity\User;
use App\Entity\Sortie;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SortieFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // === 1. Création des Sites ===
        $siteNantes = new Site();
        $siteNantes->setNom("Nantes");
        $manager->persist($siteNantes);

        $siteRennes = new Site();
        $siteRennes->setNom("Rennes");
        $manager->persist($siteRennes);

        // === 2. Création des Villes ===
        $villeNantes = new Ville();
        $villeNantes->setNom("Nantes");
        $villeNantes->setCodePostal(44000);
        $manager->persist($villeNantes);

        $villeRennes = new Ville();
        $villeRennes->setNom("Rennes");
        $villeRennes->setCodePostal(35000);
        $manager->persist($villeRennes);

        // === 3. Création des Lieux ===
        $lieuParc = new Lieu();
        $lieuParc->setNom("Parc de la Gaudinière");
        $lieuParc->setRue("Rue de la Gaudinière");
        $lieuParc->setLatitude(47.234);
        $lieuParc->setLongitude(-1.56);
        $lieuParc->setIdVille($villeNantes);
        $manager->persist($lieuParc);

        $lieuCentre = new Lieu();
        $lieuCentre->setNom("Centre-ville Rennes");
        $lieuCentre->setRue("Place de la République");
        $lieuCentre->setLatitude(48.111);
        $lieuCentre->setLongitude(-1.679);
        $lieuCentre->setIdVille($villeRennes);
        $manager->persist($lieuCentre);

        // === 4. Création des États ===
        $etatOuvert = new Etat();
        $etatOuvert->setLibelle("Ouvert");
        $manager->persist($etatOuvert);

        $etatCloture = new Etat();
        $etatCloture->setLibelle("Clôturé");
        $manager->persist($etatCloture);

        $etatCloture = new Etat();
        $etatCloture->setLibelle("Annulée");
        $manager->persist($etatCloture);

        $etatCloture = new Etat();
        $etatCloture->setLibelle("Créée");
        $manager->persist($etatCloture);

        $etatCloture = new Etat();
        $etatCloture->setLibelle("Activité en cours");
        $manager->persist($etatCloture);

        $etatCloture = new Etat();
        $etatCloture->setLibelle("Passée");
        $manager->persist($etatCloture);


        // === 5. Création des Utilisateurs ===
        $user1 = new User();
        $user1->setEmail("jean.dupont@campus-eni.fr");
        $user1->setNom("Dupont");
        $user1->setPrenom("Jean");
        $user1->setTelephone("0600000001");
        $user1->setIdSite($siteNantes);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, "password"));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail("marie.durand@campus-eni.fr");
        $user2->setNom("Durand");
        $user2->setPrenom("Marie");
        $user2->setTelephone("0600000002");
        $user2->setIdSite($siteRennes);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, "password"));
        $manager->persist($user2);

        // === 6. Création des Sorties ===
        $sortie1 = new Sortie();
        $sortie1->setNom("Pique-nique au parc");
        $sortie1->setDateHeureDebut(new DateTimeImmutable('2025-09-15 12:00:00'));
        $sortie1->setDuree(180);
        $sortie1->setDateLimiteInscription(new DateTimeImmutable('2025-09-10 23:59:59'));
        $sortie1->setNbInscriptionsMax(10);
        $sortie1->setInfosSortie("Pique-nique convivial au parc.");
        $sortie1->setIdSite($siteNantes);
        $sortie1->setIdLieu($lieuParc);
        $sortie1->setIdEtat($etatOuvert);
        $sortie1->setIdOrganisateur($user1);
        $sortie1->addListParticipant($user1);
        $sortie1->addListParticipant($user2);
        $manager->persist($sortie1);

        $sortie2 = new Sortie();
        $sortie2->setNom("Visite guidée Rennes");
        $sortie2->setDateHeureDebut(new DateTimeImmutable('2025-10-01 14:00:00'));
        $sortie2->setDuree(120);
        $sortie2->setDateLimiteInscription(new DateTimeImmutable('2025-09-25 23:59:59'));
        $sortie2->setNbInscriptionsMax(15);
        $sortie2->setInfosSortie("Visite culturelle du centre-ville.");
        $sortie2->setIdSite($siteRennes);
        $sortie2->setIdLieu($lieuCentre);
        $sortie2->setIdEtat($etatCloture);
        $sortie2->setIdOrganisateur($user2);
        $sortie2->addListParticipant($user2);
        $manager->persist($sortie2);

        // Sauvegarde
        $manager->flush();
    }
}
