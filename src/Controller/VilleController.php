<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lieu", name="lieu_")
 */
class VilleController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        LieuRepository $lieuRepository): Response
    {
        $ville = new Ville();

        $villeForm = $this->createForm(VilleType::class, $ville);

        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()) {
            $nomLieu = $villeForm["Lieu"]->getData()->getNom();
            $lieu = $lieuRepository->findOneBy(['nom' => $nomLieu]);
            $ville->setIdLieu($lieu);
//            dd($ville);
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_create');
        }

        return $this->render('ville/create.html.twig', [
            'villeForm' => $villeForm->createView()
        ]);
    }
}
