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
     * Edits email before sending.
     *
     * @Route("/edit", name="mail_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request)
    {
        $session = $request->getSession();
        $mailData = $session->get(Mail::SESSION_KEY);
        if(!$mailData)
        {
            $mailData = new Mail();
            $mailData->setSubject('Betreff')->setText('Text');
            $session->set(Mail::SESSION_KEY, $mail);
        }
        $sender = $mailData->getSender();
        if(!$mailData->getSender())
        {
            $mailData->setSender('no-reply@clanx.ch');
        }
        $editForm = $this->createForm('AppBundle\Form\Mail2Type', $mailData);
        return $this->render('mail/mail_edit.html.twig',array(
            'mail_form' => $editForm->createView(),
            'mail' => $mailData,
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
        $mailData = $session->remove(Mail::SESSION_KEY);
        $mailForm = $this->createForm('AppBundle\Form\Mail2Type', $mailData);
        $mailForm->handleRequest($request);

        if ($mailForm->isSubmitted() && $mailForm->isValid())
        {
            $message = \Swift_Message::newInstance();
            $ackMessage = \Swift_Message::newInstance();
            $message->setSubject($mailData->getSubject())
                ->setFrom($mailData->getSender())
                ->addPart(
                    $mailData->getText(),
                    'text/plain');

            if($mailData->getRecipient())
            {
                $message->setTo($mailData->getRecipient());
            }
            if($mailData->getCcs())
            {
                foreach ($mailData->getCcs() as $adr => $name) {
                    $message->addCc($adr);
                }
            }
            if($mailData->getBccs())
            {
                foreach ($mailData->getBccs() as $adr => $name) {
                    $message->addBcc($adr);
                }
            }

            $ackMessage
                ->setFrom(array('no-reply@clanx.ch' => 'Clanx Hölfer DB'))
                ->setTo($this->getUser()->getEmail())
                ->setSubject($mailData->getSubject())
                ->setBody(
                    $this->renderView('mail/send_mail_ack.html.twig',array(
                            'Mail' => $mailData,
                            'Forename' => $this->getUser()->getForename(),
                    )), 'text/html');

            $mailer = $this->get('mailer');
            $numSent = $mailer->send($message);
            $mailer->send($ackMessage);

            if ($numSent)
            {
                $this->addFlash('success', 'EMail gesendet.');
            }
            else
            {
                $this->addFlash('warning','Keine Email gesendet.');
            }

            $redirectInfo = $session->remove(RedirectInfo::SESSION_KEY);
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
         $session->remove(Mail::SESSION_KEY);
         $redirectInfo = $session->remove(RedirectInfo::SESSION_KEY);
         if(!$redirectInfo)
         {
             $redirectInfo = new RedirectInfo();
             $redirectInfo->setRouteName('dashboard_index');
         }
         $this->addFlash('warning','Email wurde nicht gesendet.');

         $args = $redirectInfo->getArguments();
         if(!$args)
         {
             $args = array();
         }
         return $this->redirectToRoute($redirectInfo->getRouteName(),$args);
     }
}
