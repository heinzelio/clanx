<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Twig_Environment;
use App\ViewModel\Email\CommitmentConfirmationViewModel;
use App\Entity\Commitment;
use App\Entity\Department;
use App\Entity\User;

class MailBuilderService implements IMailBuilderService
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
     * email address as a string.
     * @var string
     */
    private $sender;
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
     * @return string
     */
    public function getSender(){ return $this->sender; }

    /**
     * @param array sender
     * @return self
     */
    public function setSender(string $sender)
    {
        $this->sender = $sender;
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
            ->setSender('no-reply@clanx.ch')
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
            ->setSender($user->getEmail())
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
        $this->AssertNotNull($commitment->getUser());
        $this->AssertNotNull($commitment->getEvent());
        $this->AssertNotNull($operator);
        $this->AssertNotNullOrEmpty($operator->getEmail());

        $event = $commitment->getEvent();
        $volunteer = $commitment->getUser();

        $templateValues = array(
                'text' => $text,
                'event' => $event,
                'operator' => $operator,
                'volunteer' => $volunteer,
            );

        //TODO Localize
        $this->setSubject('Deine Anmeldung am '.(string)$event.' - Änderung!')
            ->setSender($operator->getEmail())
            ->setTo($volunteer->getEmail())
            ->setHtmlTemplate('emails\commitment_changed.html.twig')
            ->setHtmlTemplateValues($templateValues)
            ->setTextTemplate('emails\commitment_changed.txt.twig')
            ->setTextTemplateValues($templateValues);

        return $this->buildMessage();
    }

    public function buildDepartmentChangeNotification(
        $messageToVolunteer,
        Department $newDepartment,
        Department $oldDepartment,
        User $operator,
        User $volunteer
    )
    {
        $event = $newDepartment->getEvent();

        $templateValues = array(
            'text' => $messageToVolunteer,
            'newDepartment' => $newDepartment,
            'oldDepartment' => $oldDepartment,
            'event' => $event,
            'operator' => $operator,
            'volunteer' => $volunteer,
        );

        $this->setSubject('Deine Anmeldung am '.(string)$event.' - Ressortänderung!')
            ->setSender($operator->getEmail())
            ->setTo($volunteer->getEmail())
            ->setHtmlTemplate('emails/department_changed.html.twig')
            ->setHtmlTemplateValues($templateValues)
            ->setTextTemplate('emails/department_changed.txt.twig')
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
            $this->getSender(),
            $this->getTo(),
            $this->twig->render($this->getHtmlTemplate(), $this->getHtmlTemplateValues()),
            $this->twig->render($this->getTextTemplate(), $this->getTextTemplateValues())
        );
    }

    /**
     * Throws an exception, if a value is null.
     * @param mixed $value
     */
    private function AssertNotNull($value)
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
    private function AssertNotNullOrEmpty($value)
    {
        if ($value==null || $value=="") {
            //TODO Localize
            throw new \Exception("Not sufficient data to build a message", 1);
        }
    }

    private function buildMessageInternal($subject, $sender, $to, $bodyHtml, $bodyText)
    {
        $this->AssertNotNullOrEmpty($subject);
        $this->AssertNotNullOrEmpty($sender);
        $this->AssertNotNullOrEmpty($to);
        $this->AssertNotNullOrEmpty($bodyHtml);
        $this->AssertNotNullOrEmpty($bodyText);

        $message = new \Swift_Message();
        $message->setSubject($subject)
            ->setFrom('no-reply@clanx.ch', 'Clanx Hölfer DB')
            ->setSender($sender)
            ->setTo($to)
            ->setBody($bodyHtml, 'text/html')
            ->addPart($bodyText, 'text/plain')
            ;

        return $message;
    }
}

?>
