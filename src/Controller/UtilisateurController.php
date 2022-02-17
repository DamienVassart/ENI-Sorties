<?php

namespace App\Controller;

use App\Form\DeleteUserType;
use App\Form\UtilisateurType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/profil", name="app_profil")
     */
    public function profil(EntityManagerInterface $entityManager,
                           Request $request,
                           UserPasswordHasherInterface $userPasswordHasher,
                            ParticipantRepository $participantRepository): Response
    {
        // récupère l'utilisateur courant
        $user = $this->getUser();

        $pseudo = $user->getUserIdentifier();

        $participant = $participantRepository->findBy(['pseudo' => $pseudo], ['pseudo' => 'ASC'], 1, 0)[0];

        // création formulaire d'edition
        $form = $this->createForm(UtilisateurType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$user->getPassword() ) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }

            // récupère les infos du formulaire dans un objet $file
            $file = $form->get('champ')->getData();

            if ($file) {
                // on renomme le fichier
                $newFileName = $participant->getPseudo()."-".$participant->getId().".".$file->guessExtension();

                // traitement du fichier
                $file->move($this->getParameter('upload_champ_entite_dir'), $newFileName);

                $participant->setChamp($newFileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('Succès!', 'Votre profil a bien été modifié.');

            return $this->redirectToRoute('app_profil');
        }

        // vers Mon Profil
        return $this->render('security/profil.html.twig', [
            'profilUpdateForm' => $form->createView(),
            'participant' => $participant
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

    /**
     * @Route("/motDePasse", name="app_mdp")
     */
    public function motDePasse(ParticipantRepository $participantRepository,
                            EntityManagerInterface $entityManager,
                            Request $request,
                           UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $formForgotPassword = $this->createForm(DeleteUserType::class);
        $formForgotPassword->remove('pseudo');

        $formForgotPassword->add('pseudo', TextType::class, [
            'label' => 'Pseudo: '
        ]);
        $formForgotPassword->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
            'invalid_message' => 'Les mots de passe sont différents.',
            'required' => false,
            'first_options' => ['label' => ' '],
            'second_options' => ['label' => 'Confirmation du mot de passe: ']
        ]);
        $formForgotPassword->add('verifMdp', IntegerType::class, [
            'label' => 'Suite de chiffres de sécurité: '
        ]);

        $formForgotPassword->handleRequest($request);

        if ($formForgotPassword->isSubmitted() && $formForgotPassword->isValid()) {

            $utilisateurPseudo = $formForgotPassword['pseudo']->getData();

            $utilisateurMdp = $formForgotPassword['password']->getData();

            $verifMdp = $formForgotPassword['verifMdp']->getData();

            $userMdpPerdu = $participantRepository->findOneBy(['pseudo' => $utilisateurPseudo], ['pseudo' => 'ASC']);

            if ($userMdpPerdu->getVerifMdp() === $verifMdp)
            {
                $userMdpPerdu->setPassword($userPasswordHasher->hashPassword($userMdpPerdu, $utilisateurMdp));
                $entityManager->persist($userMdpPerdu);
                $entityManager->flush();

                $this->addFlash('Succès!', 'Le mot de passe a bien été réinitialisé.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/passwordForgotten.html.twig', [
            'formForgotPassword' => $formForgotPassword->createView()
        ]);
    }

}
