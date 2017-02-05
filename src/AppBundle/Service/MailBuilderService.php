<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Twig_Environment;
use AppBundle\Service\Authorization;
use AppBundle\ViewModel\Email\CommitmentConfirmationViewModel;
use AppBundle\Entity\Commitment;

class MailBuilderService
{

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function buildCommitmentConfirmation(Commitment $commitment)
    {
        $user = $commitment->getUser();
        $event = $commitment->getEvent();
        $dep = $commitment->getDepartment();
        $viewModel = new CommitmentConfirmationViewModel();
        $viewModel->setUser($commitment->getUser())
                ->setEvent($commitment->getEvent())
                ->setDepartment( $commitment->getDepartment());

        $subject = 'Clanx Hölfer Bestätigung';
        $from = array('no-reply@clanx.ch'=>'Clanx Hölfer DB');
        $to = $user->getEmail();
        $bodyHtml = $this->twig->render(
            // app/Resources/views/emails/commitmentConfirmation.html.twig
            'emails/commitmentConfirmation.html.twig',
            array('ViewModel' => $viewModel)
        );
        $bodyText = $this->twig->render(
            // app/Resources/views/emails/commitmentConfirmation.txt.twig
            'emails/commitmentConfirmation.txt.twig',
            array('ViewModel' => $viewModel)
        );

        return $this->buildMessage($subject, $from, $to, $bodyHtml, $bodyText);
    }

    public function buildNotificationToChief(Commitment $commitment)
    {
        if (!$commitment || !$commitment->getDepartment() || !$commitment->getDepartment()->getChiefUser()) {
            throw new Exception("Not sufficient data to build a message", 1);

        }

        $dep = $commitment->getDepartment();
        $chiefUser = $dep->getChiefUser();

        $subject = 'Neue Hölferanmeldung im Ressort '.$dep->getName();
        $from = array($user->getEmail() => $user);
        $to = $chiefUser->getEmail();

        $bodyHtml = $this->twig->render('emails\commitmentNotificationToChief.html.twig',
            array('chief' => $chiefUser,
                'user' => $user,
                'department' => $dep,
                'commitment' => $commitment,
            )
        );

        $bodyText = $this->twig->render('emails\commitmentNotificationToChief.txt.twig',
            array('chief' => $chiefUser,
                'user' => $user,
                'department' => $dep,
                'commitment' => $commitment,
            )
        );

        return $this->buildMessage($subject, $from, $to, $bodyHtml, $bodyText);
    }

    private function buildMessage($subject, $from, $to, $bodyHtml, $bodyText)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($bodyHtml, 'text/html')
            ->addPart($bodyText, 'text/plain')
            ;

        return $message;
    }
}

?>
