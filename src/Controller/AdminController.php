<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration')]
class AdminController extends AbstractController
{

    #[Route('/gestion-des-utilisateurs.html', name: 'admin_users', methods: 'GET')]
    public function users(UserRepository $userRepository): Response
    {
        # Récupération des utilisateurs de la BDD
        $users = $userRepository->findAll();

        # Transmission à la vue
        return $this->render('admin/users.html.twig', [
           'users' => $users
        ]);
    }

    #[Route('/creer-un-evenement.html', name: 'admin_create_event', methods: 'GET|POST')]
    public function createEvent(Request                $request,
                                EntityManagerInterface $em): Response
    {

        # Récupération de l'administrateur depuis la session de connexion
        $admin = $this->getUser();
        # $admin = $em->getRepository(User::class)->findOneBy(['email'=>'admin@test.com']);
        # $admin = $em->getRepository(User::class)->findOneByEmail('demo@evently.fr');

        # Création d'un nouvel Event
        $event = new Event();
        $event->setUser($admin);
        $event->setEventDate(new \DateTime());

        # Création du formulaire
        $form = $this->createForm(EventType::class, $event);

        # Traitement automatique des données du formulaire par sf.
        $form->handleRequest($request);

        # Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            # Notification à l'utilisateur
            $this->addFlash('success', "Félicitation, votre évènement est en ligne !");

            # Sauvegarde du user dans la BDD.
            $em->persist($event);
            $em->flush();

            # Redirection
            return $this->redirectToRoute('home');
        }

        # Passer le formulaire à la vue
        return $this->render('admin/create-event.html.twig', [
            'form' => $form
        ]);
    }
}