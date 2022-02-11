<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

        if ($campusForm->isSubmitted()&&$campusForm->isValid())
        {
                if ($campusForm->get('nom')->getData() !== null)
                {
                    $this->addFlash('success', 'Le campus a bien été ajouté !');
                    $entityManager->persist($campus);
                    $entityManager->flush();
                    return $this->redirectToRoute('admin_campus');
                }
        }
        return $this->render('admin/gestionCampus.html.twig', [
            "listeCampus" => $listeCampus,
            "campusForm" => $campusForm->createView()
        ]);
    }
    /**
     * @Route("/admin/campus/edit/{id}", name="admin_campus_edit")
     */
    public function edit(int $id, CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $campus = $campusRepository->find($id);

        $campusModifForm = $this->createForm(campusType::class, $campus);

        $campusModifForm->handleRequest($request);

        if($campusModifForm->isSubmitted() && $campusModifForm->isValid()){
            $this->addFlash('success', 'La ville a bien été modifiée !');
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('admin_campus');
        }

        return $this->render('admin/campusEdit.html.twig', [
            'listeCampus' => 'Modification du campus',
            'campus' => $campus,
            'campusModifForm' => $campusModifForm->createView()
        ]);
    }

    /**
     * @Route("/admin/campus/delete/{id}", name="admin_campus_delete")
     */
    public function delete(int $id, CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
            $campusASupprimer = $campusRepository->find($id);

            $campusSupprForm = $this->createForm(CampusType::class, $campusASupprimer);

            $campusSupprForm->remove('nom');
            $campusSupprForm->add('submit', SubmitType::class, [
                'label' => 'Supprimer ce campus ?'
            ]);

            $campusSupprForm->handleRequest($request);

            try{
                if($campusSupprForm->isSubmitted() && $campusSupprForm->isValid())
                {
                    $entityManager->remove($campusASupprimer);
                    $entityManager->flush();
                    return $this->redirectToRoute('admin_campus');
                }
            } catch (\Exception $e){
                $this->addFlash('success', 'Le campus est associé à une/des sortie(s), veuillez
                                                supprimer celle(s)-ci en premier');
                return $this->redirectToRoute('sortie_list');
            }

            return $this->render('admin/campusDelete.html.twig', [
                'campus' => $campusASupprimer,
                'campusSupprForm' => $campusSupprForm->createView()
            ]);

        }
}


