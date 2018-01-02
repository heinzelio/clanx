<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Service\IAuthorizationService;

/**
 * Partial event commitment controller.
 *
 * @Route("/event/commitment")
 */
class EventCommitmentController extends Controller
{
    public function showCommitmentAction(Request $request, Commitment $commitment)
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        $em = $this->getDoctrine()->getManager();
        $em->refresh($commitment);

        return $this->render('event/commitment.html.twig', array(
            'Commitment' => $commitment,
        ));
    }


    /**
     * Partial action from event edit.html.twig
     * @param  Event                 $event
     * @param  IAuthorizationService $auth
     * @return Response
     */
    public function listCommitmentAction(Event $event, IAuthorizationService $auth)
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        $commitments = $event->getCommitments();

        return $this->render('event\list_commitments.html.twig', array(
            'commitments' => $commitments,
            'may_edit_commitment' => $auth->mayEditOrDeleteCommitments($event)
        ));
    }
}
