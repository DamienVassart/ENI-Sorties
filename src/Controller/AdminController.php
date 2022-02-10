<?php

namespace App\Controller;

use App\Form\DeleteUserType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/villes", name="villes")
     */
    public function afficherVilles(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/campus", name="campus")
     */
    public function afficherCampus(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/supprimerUser", name="supprimerUser")
     */
    public function deleteUser(EntityManagerInterface $entityManager,
                                ParticipantRepository $participantRepository,
                                Request $request): Response
    {

        $form = $this->createForm(DeleteUserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $utilisateur = $form['pseudo']->getData();

            $userASupprimer = $participantRepository->find($utilisateur);

            $entityManager->remove($userASupprimer);
            $entityManager->flush();

            $this->addFlash('Succès!', 'Le compte de cet utilisateur a bien été supprimé.');

            return $this->redirectToRoute('sortie_list');
        }


        return $this->render('admin/suppressionUser.html.twig', [
            'suppressionUserForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/desactiverUser", name="desactiverUser")
     */
    public function desactiverAccesCompteUser(): Response
    {




        return $this->render('admin/desactivationUser.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

}
