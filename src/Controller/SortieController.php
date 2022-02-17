<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Participant;
use App\Form\LieuType;
use App\Form\SearchSortieType;
use App\Form\SortieAnnulerType;
use App\Form\SortieType;
use App\Form\UpdateSortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use App\Services\SortieStateSetter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(
        SortieRepository $sortieRepository,
        SortieStateSetter $sortieStateSetter,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager,
        Request $request,
        ParticipantRepository $participantRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        $userPseudo = $this->getUser()->getUserIdentifier();
        $idUserSession = $participantRepository->findOneBy(['pseudo' => $userPseudo], ['pseudo' => 'ASC'])->getId();

        $sortieUser = $participantRepository->find($idUserSession)->getSortiesParticipants();

        foreach ($sorties as $sortie) {
            $sortieStateSetter->updateState($sortie, $etatRepository);
            $entityManager->persist($sortie);
        }
        $entityManager->flush();

        $searchForm =$this->createForm(SearchSortieType::class);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid())
        {
            $nomSortie = $searchForm->get('nom')->getData();
            $campus = $searchForm->get('campus')->getData();
            $campusSortie = $campus->getId();
            $filtre1 = $searchForm->get('filtre1')->getData();

            $userPseudo = $this->getUser()->getUserIdentifier();
            $userEntier = $participantRepository->findOneBy(['pseudo' => $userPseudo], ['pseudo' => 'ASC']);

            if($nomSortie)
            {
                $sorties = $sortieRepository->search($nomSortie,$campusSortie, $userEntier);
            }

            if($campusSortie)
            {
                $sorties = $sortieRepository->search($nomSortie,$campusSortie, $userEntier);
            }

//            if ($filtre1)
//            {
//                $userOrganisateur = $sortie->getIdOrganisateur();
//
//                if($userOrganisateur===$userEntier) {
//                    $sorties = $sortieRepository->search($nomSortie,$campusSortie, $userEntier);
//                }
//            }

            if ($sorties == null) {
                $this->addFlash('error', 'Aucune sortie contenant ce mot clé dans son nom n\'a été trouvé, essayez en un autre.');
            }
        }

        return $this->render('sortie/list.html.twig', [
            "sorties"=> $sorties,
            'searchForm' => $searchForm->createView()
        ]);
    }
    /*
     * Auteur: Damien Vassart
     */
    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(
        int $id,
        SortieRepository $sortieRepository,
        LieuRepository $lieuRepository,
        VilleRepository $villeRepository
    ): Response
    {
        $sortie = $sortieRepository->find($id);
        $lieu = $lieuRepository->find($sortie->getIdLieu());
        $ville = $villeRepository->find($lieu->getIdVille());
        $participants = $sortie->getParticipants();
        $campus = $sortie->getIdCampus();

        return $this->render('sortie/details.html.twig', [
            "sortie"=> $sortie,
            "campus" => $campus,
            "lieu" => $lieu,
            "ville" => $ville,
            "participants" => $participants
        ]);
    }

    /*
     * Auteur: Damien Vassart
     */
    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository,
        CampusRepository $campusRepository,
        LieuRepository $lieuRepository,
        EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $user = $this->getUser()->getUserIdentifier();
        $organisateur = $participantRepository->findOneBy(['pseudo' => $user]);
        $sortie ->setIdOrganisateur($organisateur);
        $etat = $etatRepository->find(1);
        $sortie->setIdEtat($etat);

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $nomCampus = $sortieForm["Campus"]->getData()->getNom();
            $campus = $campusRepository->findOneBy(['nom' => $nomCampus]);
            $sortie->setIdCampus($campus);

            if($sortieForm["Lieu"]->getData()) {
                $nomLieu = $sortieForm["Lieu"]->getData()->getNom();
                $lieu = $lieuRepository->findOneBy(['nom' => $nomLieu]);

                $sortie->setIdLieu($lieu);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie ajoutée!');

                return $this->redirectToRoute('sortie_details', ['id'=> $sortie->getId()]);
            }

        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' =>$sortieForm->createview()
        ]);
    }

    /*
     * Auteur: Damien Vassart
     */
    /**
     * @Route("/update{id}", name="update")
     */
    public function update(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $entityManager,
        LieuRepository $lieuRepository
    ): Response
    {
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $lieu = $sortie->getIdLieu();
        $ville = $lieu->getIdVille();

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            if($sortieForm["Lieu"]->getData()) {
                $nomLieu = $sortieForm["Lieu"]->getData()->getNom();
                $lieu = $lieuRepository->findOneBy(['nom' => $nomLieu]);

                $sortie->setIdLieu($lieu);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie modifiée!');

                return $this->redirectToRoute('sortie_details', ['id'=> $sortie->getId()]);
            }

        }

        return $this->render('sortie/update.html.twig', [
            "sortie" => $sortie,
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    /*
     * Auteur: Damien Vassart
     */
    /**
     * @Route("/publish/{id}", name="publish")
     */
    public function publier(
        Sortie $sortie,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager
        ):Response
    {
        $etatPubliee = $etatRepository->find(2);
        $sortie->setIdEtat($etatPubliee);

        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/delete{id}", name="delete")
     */
    public function delete(Sortie $sortie, EntityManagerInterface $entityManager) : Response
    {
        $entityManager->remove($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/annuler{id}", name="annuler")
     */
    public function annuler(int $id, EntityManagerInterface $entityManager, Request $request,
                            EtatRepository $etatRepository,
                            SortieRepository $sortieRepository) : Response
    {
        $user = $this->getUser();
        $rolesUser = $user->getRoles();
        $pseudoUserCourant = $user->getUserIdentifier();
        $sortie = $sortieRepository->find($id);
        $sortieOrganisateur = $sortie->getIdOrganisateur();
        $pseudoOrganisateur = $sortieOrganisateur->getPseudo();
        $sortieForm = $this->createForm(SortieAnnulerType::class, $sortie);
        $sortieForm->handleRequest($request);
        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {
            foreach ($rolesUser as $role)
            {
                if($role == "ROLE_ADMIN") {
                    $idEtatAnnuler = $etatRepository->find(6);
                    $sortie->setIdEtat($idEtatAnnuler);
                    $this->addFlash('success', 'La sortie a bien été annulée !');
                    $entityManager->persist($sortie);
                    $entityManager->flush();
                    return $this->redirectToRoute('sortie_list');
                }
            }
            if($pseudoOrganisateur == $pseudoUserCourant) {
                $idEtatAnnuler = $etatRepository->find(6);
                $sortie->setIdEtat($idEtatAnnuler);
                $this->addFlash('success', 'La sortie a bien été annulée !');
                $entityManager->persist($sortie);
                $entityManager->flush();
                return $this->redirectToRoute('sortie_list');
            }
        }
        return $this->render('sortie/annulerSortie.html.twig', [
            'annulationSortieForm' =>$sortieForm->createview(),
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/inscription{id}", name="inscription")
     */
    public function inscription(int $id,
                                EntityManagerInterface $entityManager,
                                ParticipantRepository $participantRepository,
                                SortieRepository $sortieRepository) : Response
    {

        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();
        $userPseudo = $user->getUserIdentifier();
        $participant = $participantRepository->findOneBy(['pseudo' => $userPseudo], ['pseudo' => 'ASC']);

        $nbInscrits = count($sortie->getParticipants());
        $nbParticipantsMax = $sortie->getNbInscriptionsMax();

        if ($sortie->getIdEtat()->getId() == 2 && $nbInscrits <= $nbParticipantsMax) {
            $sortie->addParticipant($participant);

            $this->addFlash('success', 'Vous vous êtes bien inscrit à cette sortie !');

            $entityManager->persist($sortie);
            $entityManager->flush();
        } else {
            $this->addFlash('notice', "Il n'est plus possible de s'inscrire à cette sortie !");
        }

        return $this->redirectToRoute('sortie_list');
    }

    /**
     * @Route("/desistement{id}", name="desistement")
     */
    public function desistement(int $id,
                                EntityManagerInterface $entityManager,
                                ParticipantRepository $participantRepository,
                                SortieRepository $sortieRepository) : Response
    {
        $sortie = $sortieRepository->find($id);

        $user = $this->getUser();

        $userPseudo = $user->getUserIdentifier();

        $participant = $participantRepository->findOneBy(['pseudo' => $userPseudo], ['pseudo' => 'ASC']);

        if($sortie->getIdEtat()->getId() > 1 && $sortie->getIdEtat()->getId() < 4) {
            $sortie->removeParticipant($participant);

            $this->addFlash('success', 'Vous vous êtes bien désisté !');

            $entityManager->persist($sortie);
            $entityManager->flush();
        } else {
            $this->addFlash('notice', "Il n'est plus possible de se désister pour cette sortie !");
        }

        return $this->redirectToRoute('sortie_list');
    }

}