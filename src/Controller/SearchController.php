<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\SearchCampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
//    /**
//     * @Route("/campus/search", name="search_campus")
//     */
//
//        public function searchCampus(CampusRepository $campusRepository, Request $request, EntityManagerInterface  $entityManager):Response
//        {
//            $campus = new Campus();
//            $searchCampusForm = $this->createForm(CampusType::class, $campus);
//            $searchCampusForm->handleRequest($request);
//
//            $searchCampusForm = $this->createFormBuilder(SearchCampusType::class);
//
//            return $this->render('search/campus.html.twig', [
//                'rechercheCampus' => 'Recherche d un campus',
//                'campus' => $campus,
//                'searchCampusForm' => $searchCampusForm->createView(),
//            ]);
//        }
        public function Search () {
            return $this-> render('search/campus.html.twig');
        }

}