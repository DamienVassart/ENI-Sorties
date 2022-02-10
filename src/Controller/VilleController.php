<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ville", name="ville_")
 */
class VilleController extends AbstractController
{


    /**
     * @Route("", name="list")
     */
    public function list(VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();

        return $this->render('ville/list.html.twig', [
            "villes"=> $villes
        ]);
    }
    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(int $id, VilleRepository $villeRepository): Response
    {
        $ville = $villeRepository->find($id);

        return $this->render('ville/details.html.twig', [
            "ville"=> $ville

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
