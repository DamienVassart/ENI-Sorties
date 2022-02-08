<?php

namespace App\Controller;

use App\Entity\Lieu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu/create", name="lieu_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest($request);

        if($lieuForm->isSubmitted()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_create');
        }

        return $this->render('lieu/index.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }
}
