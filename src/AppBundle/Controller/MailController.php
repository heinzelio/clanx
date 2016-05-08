<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;
use AppBundle\Entity\Mail;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Form\Mail2Type;

/**
 * Mail controller.
 *
 * @Route("/mail")
 */
class MailController extends Controller
{
    /**
     * Sends a mail to a recipient.
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
    /**
     * Edits email before sending.
     *
     * @Route("/edit", name="mail_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request)
    {
        $session = $request->getSession();
        $mail = $session->get('mail');
        if(!$mail)
        {
            $mail = new Mail();
            $mail->setSubject('Betreff')->setText('Text');
            $session->set('mail', $mail);
        }
        $sender = $mail->getSender();
        if(!$mail->getSender())
        {
            $mail->setSender('noreply@clanx.ch');
        }
        $editForm = $this->createForm('AppBundle\Form\Mail2Type', $mail);
        return $this->render('mail/mail_edit.html.twig',array(
            'mail_form' => $editForm->createView(),
            'mail' => $mail,
        ));
    }

    /**
     * Actually sends the mail.
     *
     * @Route("/send", name="mail_send")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function sendAction(Request $request)
    {
        $session = $request->getSession();
        $mail = $session->remove('mail');
        $mailForm = $this->createForm('AppBundle\Form\Mail2Type', $mail);
        $mailForm->handleRequest($request);

        if ($mailForm->isSubmitted() && $mailForm->isValid())
        {
            $message = \Swift_Message::newInstance();
            $message->setSubject($mail->getSubject())
                ->setFrom($mail->getSender())
                ->addPart(
                    $mail->getText(),
                    'text/plain');

            if($mail->getRecipient())
            {
                $message->setTo($mail->getRecipient());
            }
            if($mail->getCcs())
            {
                foreach ($mail->getCcs() as $adr => $name) {
                    $message->addCc($adr);
                }
            }
            if($mail->getBccs())
            {
                foreach ($mail->getBccs() as $adr => $name) {
                    $message->addBcc($adr);
                }
            }

            $mailer = $this->get('mailer');
            $numSent = $mailer->send($message);

            if ($numSent)
            {
                $session->getFlashBag()->add('success', $numSent.' EMails verschickt.');
            }
            else
            {
                $session->getFlashBag()->add('warning','Keine Email verschickt.');
            }

            $redirectInfo = $session->remove('redirectInfo');
            if(!$redirectInfo)
            {
                $redirectInfo = new RedirectInfo();
                $redirectInfo->setRouteName('dashboard_index');
            }

            $args = $redirectInfo->getArguments();
            if(!$args)
            {
                $args = array();
            }
            return $this->redirectToRoute($redirectInfo->getRouteName(),$args);
        }
    }

    /**
     * Aborts sending of emails.
     *
     * @Route("/abort", name="mail_abort")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
     public function abortAction(Request $request)
     {
         $session = $request->getSession();
         $session->remove('mail');
         $redirectInfo = $session->remove('redirectInfo');
         if(!$redirectInfo)
         {
             $redirectInfo = new RedirectInfo();
             $redirectInfo->setRouteName('dashboard_index');
         }
         $session->getFlashBag()->add('warning','Email wurde nicht gesendet.');

         $args = $redirectInfo->getArguments();
         if(!$args)
         {
             $args = array();
         }
         return $this->redirectToRoute($redirectInfo->getRouteName(),$args);
     }
}
