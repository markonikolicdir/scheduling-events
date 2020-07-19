<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    /**
     * @Route("/api/calendar", name="calendar")
     */
    public function index(EventRepository $eventRepository)
    {
        $events = [];
        foreach ($eventRepository->eventsForCalendar() as $event){
            $temp = [];
            $temp['title'] = $event['description'];
            $temp['start'] = $event['date']->format('Y-m-d');

            $events[] = $temp;
        }

        return $this->render('calendar/index.html.twig', [
            'events' => json_encode($events),
        ]);
    }
}
