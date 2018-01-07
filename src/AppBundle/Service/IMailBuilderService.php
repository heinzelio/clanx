<?php
namespace AppBundle\Service;

use AppBundle\Entity\Commitment;
use AppBundle\Entity\User;

interface IMailBuilderService
{
    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string subject
     * @return self
     */
    public function setSubject($subject);

    /**
     * @return array
     */
    public function getFrom();

    /**
     * @param array from
     * @return self
     */
    public function setFrom(array $from);

    /**
     * @return string
     */
    public function getTo();

    /**
     * @param string to
     * @return self
     */
    public function setTo($to);

    /**
     * @return string
     */
    public function getHtmlTemplate();

    /**
     * @param string htmlTemplate
     * @return self
     */
    public function setHtmlTemplate($htmlTemplate);

    /**
     * @return array
     */
    public function getHtmlTemplateValues();

    /**
     * @param array htmlTemplateValues
     * @return self
     */
    public function setHtmlTemplateValues(array $htmlTemplateValues);

    /**
     * @return string
     */
    public function getTextTemplate();

    /**
     * @param string textTemplate
     * @return self
     */
    public function setTextTemplate($textTemplate);

    /**
     * @return array
     */
    public function getTextTemplateValues();

    /**
     * @param array textTemplateValues
     * @return self
     */
    public function setTextTemplateValues(array $textTemplateValues);

    public function buildCommitmentConfirmation(Commitment $commitment);

    public function buildNotificationToChief(Commitment $commitment);

    public function buildCommitmentVolunteerNotification($text,Commitment $commitment, User $operator);

    public function buildMessage();
}

?>