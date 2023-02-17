<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/evenement')]
class EventController extends AbstractController
{
    #[Route('/{id}/participer.html', name: 'event_join', methods: 'GET')]
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


    #[Route('/creer-un-evenement.html', name: 'event_create', methods: 'GET|POST')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
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
            return $this->redirectToRoute('default_event', [
                'id' => $event->getId()
            ]);
        }

        # Passer le formulaire à la vue
        return $this->render('admin/create-event.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/modifier.html', name: 'event_update', methods: 'GET|POST')]
    #[IsGranted(new Expression('is_granted("IS_AUTHENTICATED_FULLY") and object.isOwner(user)'), 'event')]
    public function updateEvent(Request                $request,
                                Event                  $event,
                                EntityManagerInterface $em): RedirectResponse|Response
    {
        # Création du formulaire
        $form = $this->createForm(EventType::class, $event);

        # Traitement automatique des données du formulaire par sf.
        $form->handleRequest($request);

        # Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            # Notification à l'utilisateur
            $this->addFlash('success', "Félicitation, votre évènement a été mis à jour !");

            # Sauvegarde du user dans la BDD.
            $em->flush();

            # Redirection
            return $this->redirectToRoute('default_event', [
                'id' => $event->getId()
            ]);
        }

        # Passer le formulaire à la vue
        return $this->render('admin/create-event.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/supprimer.html', name: 'event_delete', methods: 'GET')]
    ##[IsGranted(new Expression('is_granted("IS_AUTHENTICATED_FULLY") and object.isOwner(user)'), 'event')]
    #[Security('is_granted("IS_AUTHENTICATED_FULLY") and event.isOwner(user)')]
    public function deleteEvent(Event                  $event,
                                EventRepository $eventRepository): RedirectResponse|Response
    {
        # Supression de l'évènement
        $eventRepository->remove($event, true);

        # Notification
        $this->addFlash('success', "Félicitation, votre évènement a bien été supprimé !");

        # Redirection
        return $this->redirectToRoute('user_events');

    }

}