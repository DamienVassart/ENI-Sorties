<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\SearchCampusType;
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
        $searchForm = $this->createForm(SearchCampusType::class);
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

        $searchForm->handleRequest($request);
        $nomCampus = $searchForm->get('nom')->getData();
        $listeCampus = $campusRepository->searchCampus($nomCampus);

        if ($campusForm->isSubmitted()&&$campusForm->isValid())
        {
            if ($listeCampus==null)
            {
                $this->addFlash('error', 'Aucun campus contenant ce mot clé dont ce nom n\'a été trouvé, essayez en un autre.');
            }
        }

        return $this->render('admin/gestionCampus.html.twig', [
            "listeCampus" => $listeCampus,
            "campusForm" => $campusForm->createView(),
            "searchForm" => $searchForm->createView(),
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
     * @Route("/admin/campus/delete{id}", name="admin_campus_delete")
     */
    public function delete(Campus $campus, CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();
        $this->addFlash('success', 'Le campus a bien été supprimé');
        return $this->redirectToRoute('admin_campus');
    }
}


