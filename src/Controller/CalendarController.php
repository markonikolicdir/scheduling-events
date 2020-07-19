<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/calendar")
 */
class CalendarController extends AbstractController
{
    /**
     * @Route("/", name="calendar_index")
     */
    public function index(EventRepository $eventRepository)
    {
        $events = [];
        foreach ($eventRepository->eventsForCalendar() as $event){
            $temp = [];
            $temp['id'] = $event['id'];
            $temp['title'] = $event['description'];
            $temp['start'] = $event['date']->format('Y-m-d');

            $events[] = $temp;
        }

        return $this->render('calendar/index.html.twig', [
            'events' => json_encode($events),
        ]);
    }

    /**
     * @Route("/new", name="calendar_new", methods={"POST"})
     */
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $event = new Event();
        $date = \DateTime::createFromFormat('Y-m-d', $request->request->get('date'));

        $event->setDate($date);
        $event->setDescription($request->request->get('description'));

        $errors = $validator->validate($event);

        if (count($errors) > 0) {

            $message = [];
            foreach ($errors as $violation) {
                $message[] = $violation->getMessage();
            }

            return new JsonResponse(['status'=>JsonResponse::HTTP_UNPROCESSABLE_ENTITY, 'message' =>$message]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();

        return new JsonResponse(['status'=>JsonResponse::HTTP_CREATED]);
    }

    /**
     * @Route("/{id}", name="calendar_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Event $event): Response
    {
//        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
//        }

        return new JsonResponse(['status'=>JsonResponse::HTTP_NO_CONTENT]);
//        return $this->redirectToRoute('event_index');
    }
}
