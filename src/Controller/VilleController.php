<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    /**
     * @Route("/admin/villes", name="admin_villes")
     */
    public function villes(VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
       $listeVilles = $villeRepository->findAll();

       $ville = new Ville();
       $villeForm = $this->createForm(VilleType::class, $ville);
       $villeForm->handleRequest($request);

       if($villeForm->isSubmitted()&&$villeForm->isValid())
       {
           $this->addFlash('success', 'La ville a bien été ajoutée !');
           $entityManager->persist($ville);
           $entityManager->flush();
           return $this->redirectToRoute('admin_villes');
       }

        return $this->render('admin/gestionVilles.html.twig', [
            'listeVilles' => $listeVilles,
            'villeForm' => $villeForm->createView()
        ]);
    }
}

