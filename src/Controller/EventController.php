<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evenement')]
class EventController extends AbstractController
{
    #[Route('/participer/{id}', name: 'event_join', methods: 'GET')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function join(Event $event, EntityManagerInterface $entityManager): RedirectResponse
    {
        # On ajoute à l'évènement un participant
        $event->addAttendee(
            # Le participant est l'utilisateur connecté
            $this->getUser()
        );

        # On sauvegarde dans la BDD
        $entityManager->flush();

        # On notifie l'utilisateur
        $this->addFlash('success', "Merci, votre participation à bien été prise en compte !");

        # Redirection sur la page de l'évènement.
        return $this->redirectToRoute('default_event', [
            'id' => $event->getId()
        ]);
    }
}