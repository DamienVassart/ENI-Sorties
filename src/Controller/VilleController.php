<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\ModifierVilleType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        return $this->render('admin/gestionVilles.html.twig', [
            'listeVilles' => $listeVilles,
            'villeForm' => $villeForm->createView()
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
    public function delete(int $id, VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $villeASupprimer = $villeRepository->find($id);

        $villeSupprForm = $this->createForm(VilleType::class, $villeASupprimer);

        $villeSupprForm->remove('nom');
        $villeSupprForm->remove('codePostal');
        $villeSupprForm->add('submit', SubmitType::class, [
            'label' => 'Supprimer cette ville ?'
        ]);

        $villeSupprForm->handleRequest($request);

        try{
            if($villeSupprForm->isSubmitted() && $villeSupprForm->isValid())
            {
                $entityManager->remove($villeASupprimer);
                $entityManager->flush();
                return $this->redirectToRoute('admin_villes');
            }
        } catch (\Exception $e){
            $this->addFlash('success', 'La ville est associée à une/des sortie(s), veuillez
                                                supprimer celle(s)-ci en premier');
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('admin/villeDelete.html.twig', [
            'ville' => $villeASupprimer,
            'villeSupprForm' => $villeSupprForm->createView()
        ]);

    }
}

