<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->findAll();

        return $this->render('campus/list.html.twig', [
            "campus"=> $campus
        ]);
    }
    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(int $id, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);

        return $this->render('campus/details.html.twig', [
            "campus"=> $campus

        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        LieuRepository $lieuRepository): Response
    {
        $campus = new Campus();

        $campusForm = $this->createForm(CampusType::class, $campus);

        $campusForm->handleRequest($request);

        if($campusForm->isSubmitted() && $campusForm->isValid()) {
            $nomLieu = $campusForm["Lieu"]->getData()->getNom();
            $lieu = $lieuRepository->findOneBy(['nom' => $nomLieu]);
            $campus->setIdLieu($lieu);
//            dd($campus);
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_create');
        }

        return $this->render('campus/create.html.twig', [
            'campusForm' => $campusForm->createView()
        ]);
    }
}
