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

    private function renderAdminView(Event $event)
    {
        // nothing special so far. Show the same as for committee members
        return $this->renderChiefView($event);
    }

    private function renderCommitteeView(Event $event)
    {
        $commitments = $event->getCommitments();
        $tickets = 0;
        $maleShirtSizes = array('XS'=>0,'S'=>0,'M'=>0,'L'=>0,'XL'=>0,'XXL'=>0,);
        $femaleShirtSizes = array('XS'=>0,'S'=>0,'M'=>0,'L'=>0,'XL'=>0,'XXL'=>0,);
        foreach ($commitments as $commitment) {
            if($commitment->getNeedTrainTicket()){
                $tickets++;
            }
            // TODO refactor.
            if('M' == $commitment->getUser()->getGender()){
                if(!array_key_exists($commitment->getShirtSize(), $maleShirtSizes))
                {
                    $maleShirtSizes[$commitment->getShirtSize()] = 0;
                }
                $maleShirtSizes[$commitment->getShirtSize()]++;
            }
            else
            {
                if(!array_key_exists($commitment->getShirtSize(), $femaleShirtSizes)){
                    $femaleShirtSizes[$commitment->getShirtSize()] = 0;
                }
                $femaleShirtSizes[$commitment->getShirtSize()]++;
            }

        }
        return $this->render('event/statistics.html.twig', array(
            'event' => $event,
            'tickets' => $tickets,
            'maleShirtSizes' => $maleShirtSizes,
            'femaleShirtSizes' => $femaleShirtSizes,
        ));
    }
}
