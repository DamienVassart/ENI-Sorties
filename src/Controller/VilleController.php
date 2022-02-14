<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Form\ModifierVilleType;
use App\Form\SearchVilleType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $searchForm = $this->createForm(SearchVilleType::class);

       $villeForm->handleRequest($request);

       if($villeForm->isSubmitted()&&$villeForm->isValid())
       {

           if($villeForm->get('nom')->getData() !== null && $villeForm->get('codePostal')->getData() !== null)
           {
               $this->addFlash('success', 'La ville a bien été ajoutée !');
               $entityManager->persist($ville);
               $entityManager->flush();
               return $this->redirectToRoute('admin_villes');
           }

       }

        $searchForm->handleRequest($request);

        $nomVille = $searchForm->get('nom')->getData();
        $listeVilles = $villeRepository->searchCities($nomVille);

       if ($searchForm->isSubmitted() && $searchForm->isValid())
        {

            if ($listeVilles == null) {
                $this->addFlash('error', 'Aucune ville contenant ce mot clé dans son nom n\'a été trouvé, essayez en un autre.');
            }
        }

        return $this->render('admin/gestionVilles.html.twig', [
            'listeVilles' => $listeVilles,
            'villeForm' => $villeForm->createView(),
            'searchForm' => $searchForm->createView(),
        ]);
    }

    /**
     * @Route("/admin/villes/edit/{id}", name="admin_villes_edit")
     */
    public function edit(int $id, VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = $villeRepository->find($id);

        $villeModifForm = $this->createForm(VilleType::class, $ville);

        $villeModifForm->handleRequest($request);

        if($villeModifForm->isSubmitted() && $villeModifForm->isValid()) {
            $this->addFlash('success', 'La ville a bien été modifiée !');
            $entityManager->persist($ville);
            $entityManager->flush();
            return $this->redirectToRoute('admin_villes');
        }

        return $this->render('admin/villeEdit.html.twig', [
            'ville' => $ville,
            'villeModifForm' => $villeModifForm->createView()
        ]);
    }

    /**
     * @Route("/admin/villes/delete{id}", name="admin_villes_delete")
     */
    public function delete(Ville $ville, VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ville);
        $entityManager->flush();
        $this->addFlash('success', 'La ville a bien été supprimé');
        return $this->redirectToRoute('admin_villes');
    }

}

