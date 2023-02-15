<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * Page d'Accueil
     */
    public function home(EventRepository $eventRepository): Response
    {
        # Récupération des évènements de la base de données.
        $events = $eventRepository->findAll();

        # Passer les évènements à ma vue
        return $this->render('default/home.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/evenement/{id}', name: 'default_event', methods: 'GET')]
    public function event(Event $event = null)
    {

        # Je vérifie si mon "event" est null (pas de correspondance dans la bdd)
        if($event === null) {
            # Je redirige l'utilisateur sur la page d'accueil
            return $this->redirectToRoute('home');
        }

        return $this->render('default/event.html.twig', [
            'event' => $event
        ]);
    }

}