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
 * Partial event commitment controller.
 *
 * @Route("/event/commitment")
 */
class EventCommitmentController extends Controller
{
    /**
     * Shows the enroll view.
     *
     * @Route("/{id}", name="event_statistics_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Event $event)
    {
        $authService = $this->get('app.auth');
        if(!$authService->mayEnroll($event)){
            return $this->render('event/may_not_enroll.html.twig');
        }
        return $this->render('event/enroll.html.twig');
    }

}
