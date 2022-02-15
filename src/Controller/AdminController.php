<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\DeleteUserType;
use App\Form\DesactivateUserType;
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
     * @Route("/index", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
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



        if ($form->isSubmitted() && $form->isValid()) {
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
    public function desactiverAccesCompteUser(EntityManagerInterface $entityManager,
                                              ParticipantRepository $participantRepository,
                                              Request $request): Response
    {
        $desactiverForm = $this->createForm(DesactivateUserType::class);

        $desactiverForm->handleRequest($request);

        if ($desactiverForm->isSubmitted() && $desactiverForm->isValid()) {

            $utilisateurPseudo = $desactiverForm['pseudo']->getData();

            $userADesactiver = $participantRepository->find($utilisateurPseudo);

            $userADesactiver->setRoles(['ROLE_DESACTIVATE']);
            $entityManager->persist($userADesactiver);
            $entityManager->flush();

            $this->addFlash('Succès!', 'Le compte de cet utilisateur a bien été désactivé.');

            return $this->redirectToRoute('sortie_list');
        }


        return $this->render('admin/desactivationUser.html.twig', [
            'desactiverForm' => $desactiverForm->createView(),
        ]);

    }

}
