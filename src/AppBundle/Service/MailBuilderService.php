<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Twig_Environment;
use AppBundle\Service\Authorization;
use AppBundle\ViewModel\Email\CommitmentConfirmationViewModel;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\User;

class MailBuilderService
{

    /**
     * @var Twig_Environment
     */
    private $twig;
    /**
     * @var string
     */
    private $subject;
    /**
     * this array must be in the form (sender@mail.com => senderName)
     * @var array
     */
    private $from;
    /**
     * email address as a string.
     * @var string
     */
    private $to;
    /**
     * Path to the html template. From \app\Resources\views\.
     * Must be a twig template.
     * Example: "emails/file.html.twig"
     * @var string
     */
    private $htmlTemplate;
    /**
     * An array with all the values that are needed in the $htmlTemplate.
     * @var array
     */
    private $htmlTemplateValues;
    /**
     * Path to the text template. From \app\Resources\views\.
     * Must be a twig template.
     * Example: "emails/file.txt.twig"
     * @var string
     */
    private $textTemplate;
    /**
     * An array with all the values that are needed in the $textTemplate.
     * @var array
     */
    private $textTemplateValues;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }


    /**
     * @return string
     */
    public function getSubject(){ return $this->subject; }

    /**
     * @param string subject
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return array
     */
    public function getFrom(){ return $this->from; }

    /**
     * @param array from
     * @return self
     */
    public function setFrom(array $from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    public function getTo(){ return $this->to; }

    /**
     * @param string to
     * @return self
     */
    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlTemplate(){ return $this->htmlTemplate; }

    /**
     * @param string htmlTemplate
     * @return self
     */
    public function setHtmlTemplate($htmlTemplate)
    {
        $this->htmlTemplate = $htmlTemplate;
        return $this;
    }

    /**
     * @return array
     */
    public function getHtmlTemplateValues(){ return $this->htmlTemplateValues; }

    /**
     * @param array htmlTemplateValues
     * @return self
     */
    public function setHtmlTemplateValues(array $htmlTemplateValues)
    {
        $this->htmlTemplateValues = $htmlTemplateValues;
        return $this;
    }

    /**
     * @return string
     */
    public function getTextTemplate(){ return $this->textTemplate; }

    /**
     * @param string textTemplate
     * @return self
     */
    public function setTextTemplate($textTemplate)
    {
        $this->textTemplate = $textTemplate;
        return $this;
    }

    /**
     * @return array
     */
    public function getTextTemplateValues(){ return $this->textTemplateValues; }

    /**
     * @param array textTemplateValues
     * @return self
     */
    public function setTextTemplateValues(array $textTemplateValues)
    {
        $this->textTemplateValues = $textTemplateValues;
        return $this;
    }

    /**
     * Throws an exception, if a value is null.
     * @param mixed $value
     */
    public function AssertNotNull($value)
    {
        if ($value==null) {
            //TODO Localize
            throw new \Exception("Not sufficient data to build a message", 1);
        }
    }

    /**
     * Throws an exception, if a value is null.
     * @param mixed $value
     */
    public function AssertNotNullOrEmpty($value)
    {
        if ($value==null || $value=="") {
            //TODO Localize
            throw new \Exception("Not sufficient data to build a message", 1);
        }
    }

    public function buildCommitmentConfirmation(Commitment $commitment)
    {
        $this->AssertNotNull($commitment);
        $this->AssertNotNull($commitment->getUser());
        $this->AssertNotNull($commitment->getEvent());
        //$this->AssertNotNull($commitment->getDepartment()); // MAY BE NULL

        $user = $commitment->getUser();
        $event = $commitment->getEvent();

        $viewModel = new CommitmentConfirmationViewModel();
        $viewModel->setUser($commitment->getUser())
                ->setEvent($commitment->getEvent())
                ->setDepartment( $commitment->getDepartment());

        $templateValues = array('ViewModel' => $viewModel);

        //TODO Localize
        $this->setSubject($event.' Anmeldungsbestätigung')
            ->setFrom(array('no-reply@clanx.ch'=>'Clanx Hölfer DB'))
            ->setTo($user->getEmail())
            ->setHtmlTemplate('emails\commitmentConfirmation.html.twig')
            ->setHtmlTemplateValues($templateValues)
            ->setTextTemplate('emails\commitmentConfirmation.txt.twig')
            ->setTextTemplateValues($templateValues);

        return $this->buildMessage();
    }

    public function buildNotificationToChief(Commitment $commitment)
    {
        $this->AssertNotNull($commitment);
        $this->AssertNotNull($commitment->getDepartment());
        $this->AssertNotNull($commitment->getDepartment()->getChiefUser());

        $user = $commitment->getUser();
        $dep = $commitment->getDepartment();
        $chiefUser = $dep->getChiefUser();

        $templateValues =
                array('chief' => $chiefUser,
                    'user' => $user,
                    'department' => $dep,
                    'commitment' => $commitment,
                );

        //TODO Localize
        $this->setSubject('Neue Hölferanmeldung im Ressort '.$dep->getName())
            ->setFrom(array($user->getEmail() => $user))
            ->setTo($chiefUser->getEmail())
            ->setHtmlTemplate('emails\commitmentNotificationToChief.html.twig')
            ->setHtmlTemplateValues($templateValues)
            ->setTextTemplate('emails\commitmentNotificationToChief.txt.twig')
            ->setTextTemplateValues($templateValues);

        return $this->buildMessage();
    }

    public function buildCommitmentVolunteerNotification($text,Commitment $commitment, User $operator)
    {
        $this->AssertNotNull($commitment);
        $this->AssertNotNull($commitment->getDepartment());
        $this->AssertNotNull($commitment->getUser());
        $this->AssertNotNull($commitment->getDepartment()->getEvent());
        $this->AssertNotNull($operator);
        $this->AssertNotNullOrEmpty($operator->getEmail());

        $event = $commitment->getDepartment()->getEvent();
        $volunteer = $commitment->getUser();

        $templateValues = array(
                'text' => $text,
                'event' => $event,
                'operator' => $operator,
                'volunteer' => $volunteer,
            );

        //TODO Localize
        $this->setSubject('Dein Einsatz am '.(string)$event.' - Änderung!')
            ->setFrom(array($operator->getEmail() => $operator))
            ->setTo($volunteer->getEmail())
            ->setHtmlTemplate('emails\commitment_changed.html.twig')
            ->setHtmlTemplateValues($templateValues)
            ->setTextTemplate('emails\commitment_changed.txt.twig')
            ->setTextTemplateValues($templateValues);

        return $this->buildMessage();

    }

    public function buildMessage()
    {
        $this->AssertNotNullOrEmpty($this->getHtmlTemplate());
        $this->AssertNotNullOrEmpty($this->getHtmlTemplateValues());
        $this->AssertNotNullOrEmpty($this->getTextTemplate());
        $this->AssertNotNullOrEmpty($this->getTextTemplateValues());

        return $this->buildMessageInternal(
            $this->getSubject(),
            $this->getFrom(),
            $this->getTo(),
            $this->twig->render($this->getHtmlTemplate(), $this->getHtmlTemplateValues()),
            $this->twig->render($this->getTextTemplate(), $this->getTextTemplateValues())
        );
    }

    private function buildMessageInternal($subject, $from, $to, $bodyHtml, $bodyText)
    {
        $this->AssertNotNullOrEmpty($subject);
        $this->AssertNotNullOrEmpty($from);
        $this->AssertNotNullOrEmpty($to);
        $this->AssertNotNullOrEmpty($bodyHtml);
        $this->AssertNotNullOrEmpty($bodyText);

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
