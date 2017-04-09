<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;

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

    public function listCommitmentAction(Event $event)
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        $commitments = $event->getCommitments();
        $auth = $this->get('app.auth');

        return $this->render('event\list_commitments.html.twig', array(
            'commitments' => $commitments,
            'may_edit_commitment' => $auth->mayEditOrDeleteCommitments($event)
        ));
    }
}
