<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/inscription.html', name: 'user_register', methods: 'GET|POST')]
    public function register(Request $request,
                             UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface $em): Response
    {
        # Création d'un nouvel utilisateur
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        # Création du formulaire
        $form = $this->createForm(UserType::class, $user);

        # Traitement automatique des données du formulaire par sf.
        $form->handleRequest($request);

        # Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            # Encodage du mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );

            # Sauvegarde du user dans la BDD.
            $em->persist($user);
            $em->flush();

            # Notification à l'utilisateur
            $this->addFlash('success', "Félicitation, votre compte est actif. 
            Vous pouvez maintenant vous connecter.");

            # Redirection sur la page de connexion
            return $this->redirectToRoute('app_login');
        }

        # Passer le formulaire à la vue
        return $this->render('user/register.html.twig', [
            'form' => $form
        ]);
    }
}