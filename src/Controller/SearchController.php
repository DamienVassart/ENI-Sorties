<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/campus/search", name="search_campus")
     */
        public function searchCampus(Request $request)
        {
            return $this->render('search/campus.html.twig');
        }

}