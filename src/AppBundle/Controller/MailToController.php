<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;

/**
 * Department controller.
 *
 * @Route("/mail")
 */
class MailToController extends Controller
{
    /**
     * Lists all Department entities.
     *
     * @Route("/to/{id}", name="mail_to")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function toAction(Request $request, User $toUser)
    {
        $mailForm = $this->createForm('AppBundle\Form\MailType');
        $mailForm->handleRequest($request);
        $session = $request->getSession();
        $subject = $session->get('mail_subject');
        $text = $session->get('mail_text');

        if ($mailForm->isSubmitted() && $mailForm->isValid())
        {
            $redirectUrl = $session->remove('tmp_redirect_url');

            $fromUser = $this->getUser();
            $subject = $mailForm->get('subject')->getData();
            $text = $mailForm->get('text')->getData();
            $message = \Swift_Message::newInstance();
            $message->setSubject($subject)
                ->setFrom($fromUser->getEmail())
                ->setTo($toUser->getEmail())
                ->addPart($text, 'text/plain');
            $this->get('mailer')->send($message);

            if($redirectUrl)
            {
                return $this->redirect($redirectUrl);
            }
            else
            {
                return $this->redirectToRoute('dashboard_index');
            }
        }

        $redirectUrl = $session->remove('redirect_url');
        $session->set('tmp_redirect_url',$redirectUrl);

        // only on unsubmitted forms:
        $mailForm->get('subject')->setData($subject);
        $mailForm->get('text')->setData($text);
        return $this->render('emails/mail_to.html.twig', array(
            'subject' => $subject,
            'mail_form' => $mailForm->createView(),
            'redirectUrl' => $redirectUrl,
            'toUser' => $toUser
        ));
    }
}
