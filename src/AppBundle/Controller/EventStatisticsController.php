<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;

/**
 * Event controller.
 *
 * @Route("/event/statistics")
 */
class EventStatisticsController extends Controller
{
    /**
     * Shows statistics.
     *
     * @Route("/{id}", name="event_statistics_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Event $event)
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->renderAdminView($event);
        }
        if($this->isGranted('ROLE_OK')){
            return $this->renderCommitteeView($event);
        }
        // regular users do not see anything.
        return new Response('');
    }

    /**
     * Renders the view for admins.
     * @param  Event  $event
     * @return view
     */
    private function renderAdminView(Event $event)
    {
        // nothing special so far. Show the same as for committee members
        return $this->renderCommitteeView($event);
    }

    /**
     * Renders the view for members of the committee.
     * @param  Event  $event
     * @return View
     */
    private function renderCommitteeView(Event $event)
    {
        $eventService = $this->get('app.event');

        $viewModels = $eventService->getStatisticsViewModels($event);

        return $this->render('event/statistics.html.twig', array(
            'viewModels' => $viewModels,
        ));
    }
}
