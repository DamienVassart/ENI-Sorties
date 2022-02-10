<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class CampusController extends AbstractController
{
    /**
     * @Route("/admin/campus", name="admin_campus")
     */
    public function campus(CampusRepository $campusRepository, Request $request, EntityManagerInterface  $entityManager):Response
    {
        $listeCampus = $campusRepository->findAll();
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted()&&$campusForm->isValid()) {

            $this->addFlash('success', 'Le campus a bien été ajouté !');
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->redirectToRoute('admin_campus');
        }

        return $this->render('admin/gestionCampus.html.twig', [
            "listeCampus" => $listeCampus,
            "campusForm" => $campusForm->createView()
        ]);
    }
}


