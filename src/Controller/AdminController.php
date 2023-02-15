<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/administration/creer-un-evenement.html', name: 'admin_create_event', methods: 'GET|POST')]
    public function createEvent(Request                $request,
                                EntityManagerInterface $em): Response
    {

        # TODO : En attendant la connexion, on récuperer manuellement l'admin
        # $admin = $em->getRepository(User::class)->findOneBy(['email'=>'admin@test.com']);
        $admin = $em->getRepository(User::class)->findOneByEmail('admin@test.com');

        # Création d'un nouvel Event
        $event = new Event();
        $event->setUser($admin);

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