<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Participant;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
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
    public function details(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/details.html.twig', [
            "sortie"=> $sortie

        ]);
    }
    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository): Response
    {
        $sortie = new Sortie();
        $user = $this->getUser()->getUserIdentifier();
        $organisateur = $participantRepository->findOneBy(['pseudo' => $user]);
        $sortie ->setIdOrganisateur($organisateur);

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);
        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted() && $sortieForm->isSubmitted()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Sortie ajoutée!');

            return $this->redirectToRoute('sortie_details', ['id'=> $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' =>$sortieForm->createview(),
            'lieuForm' =>$lieuForm->createView()
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
}
