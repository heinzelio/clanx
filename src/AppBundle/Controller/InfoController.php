<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Entity\Mail;


/**
 * Dashboard controller.
 * @Route("/info")
 */
class InfoController extends Controller
{
    /**
    * @Route("/", name="info_index")
    * @Method("GET")
    * @Security("has_role('ROLE_USER')")
    */
    public function indexAction(Request $request)
    {
        return $this->render('info/index.html.twig');
    }

    /**
    * @Route("/privacypolicy", name="info_privacy_policy")
    * @Method("GET")
    */
    public function privacyPolicyAction()
    {
        return $this->render('info/privacy_policy.html.twig');
    }

    /**
    * @Route("/mail/to/developer", name="info_mail_developer")
    * @Method("GET")
    * @Security("has_role('ROLE_USER')")
    */
    public function mailDeveloperAction(Request $request)
    {
        $session = $request->getSession();

        $mailData = new Mail();

        $mailData->setSubject("Frage/Anregung HelferDB")
             ->setSender($this->getUser()->getEmail())
             ->setRecipient('helferdb@clanx.ch')
             ->setText("Von: ".(string)$this->getUser())
             ;

        $session->set(Mail::SESSION_KEY, $mailData);

        $backLink = new RedirectInfo();
        $backLink->setRouteName('info_index')
              ->setArguments(null)
              ;

        $session->set(RedirectInfo::SESSION_KEY, $backLink);

        return $this->redirectToRoute('mail_edit');
    }
}

?>
