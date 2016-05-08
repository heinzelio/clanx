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
        $mail = $session->get(Mail::SESSION_KEY);
        if(!$mail)
        {
            $mail = new Mail();
            $mail->setSubject('Betreff')->setText('Text');
            $session->set(Mail::SESSION_KEY, $mail);
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
        $mail = $session->remove(Mail::SESSION_KEY);
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
         $session->getFlashBag()->add('warning','Email wurde nicht gesendet.');

         $args = $redirectInfo->getArguments();
         if(!$args)
         {
             $args = array();
         }
         return $this->redirectToRoute($redirectInfo->getRouteName(),$args);
     }
}
