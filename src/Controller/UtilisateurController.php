<?php

namespace App\Controller;

use App\Form\UtilisateurType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/profil", name="app_profil")
     */
    public function profil(EntityManagerInterface $entityManager,
                           Request $request): Response
    {
        // récupère l'utilisateur courant
        $user = $this->getUser();

        // création formulaire d'edition
        $form = $this->createForm(UtilisateurType::class, $user);
        if ($form->isSubmitted()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('Succès!', 'Votre profil a bien été modifié.');

            return $this->redirectToRoute('app_profil');
        }

        // vers Mon Profil
        return $this->render('security/profil.html.twig', [
            'profilUpdateForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/profilAutre/{id}", name="app_profilAutre")
     */
    public function profilAutre(int $id, ParticipantRepository $participantRepository): Response
    {
        // récupérer l'id de l'utilisateur dont on regarde les informations
        $participant = $participantRepository->find($id);

        // vers profilAutre
        return $this->render('utilisateur/profilAutre.html.twig', [
            'participant' => $participant
        ]);
    }

}
