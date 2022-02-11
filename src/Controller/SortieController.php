<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Participant;
use App\Form\LieuType;
use App\Form\SortieAnnulerType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', [
            "sorties"=> $sorties
        ]);
    }
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

        return $this->render('sortie/details.html.twig', [
            "sortie"=> $sortie,
            "lieu" => $lieu,
            "ville" => $ville
        ]);
    }
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
    public function annuler(Sortie $sortie, EntityManagerInterface $entityManager, Request $request, EtatRepository $etatRepository) : Response
    {

        $sortieForm = $this->createForm(SortieAnnulerType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid())
        {

            $idEtatAnnuler = $etatRepository->find(6);

            $sortie->setIdEtat($idEtatAnnuler);
            $this->addFlash('success', 'La sortie a bien été annulée !');
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_list');
        }


        return $this->render('sortie/annulerSortie.html.twig', [
            'annulationSortieForm' =>$sortieForm->createview(),
            'sortie' => $sortie
        ]);
    }

//    /**
//     * @Route("/inscription", name="inscription")
//     */
//    public function inscription(Sortie $sortie,
//                                EntityManagerInterface $entityManager,
//                                Request $request,
//                                EtatRepository $etatRepository,
//                                ParticipantRepository $participantRepository) : Response
//    {
//
//        $user = $this->getUser();
//
//        $sortieForm = $this->createForm(SortieAnnulerType::class, $sortie);
//
//        $sortieForm->handleRequest($request);
//
//        if($sortieForm->isSubmitted() && $sortieForm->isValid())
//        {
//
//            $idEtatAnnuler = $etatRepository->find(6);
//
//            $sortie->setIdEtat($idEtatAnnuler);
//            $this->addFlash('success', 'La sortie a bien été annulée !');
//            $entityManager->persist($sortie);
//            $entityManager->flush();
//            return $this->redirectToRoute('sortie_list');
//        }
//
//
//        return $this->render('sortie/annulerSortie.html.twig', [
//            'annulationSortieForm' =>$sortieForm->createview(),
//            'sortie' => $sortie
//        ]);
//    }
}
