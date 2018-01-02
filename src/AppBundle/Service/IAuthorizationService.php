<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
use AppBundle\Entity\Event;
use AppBundle\Entity\Question;
use AppBundle\Entity\User;

interface IAuthorizationService
{
    /**
     * Gets the current logged in user
     * @return AppBundle\Entity\User The user.
     */
    public function getUser();

    /**
     * Determines if the logged in user may send a mass email to all
     * volunteers of an event
     * @return boolean True if user may send mass email.
     */
    public function maySendEventMassMail();

    /**
     * Determines if the logged in user may edit the given event
     * @param  Event $event The event
     * @return boolean True, if the event may be edited.
     */
    public function mayEditEvent(Event $event=null);

    /**
     * Determines if the event detail may be shown to the logged in user
     * and returns an array if so or not and a message why.
     * @param  Event  $event The event.
     * @return array Array of two fields, stating if the event may be
     * shown or not and why. Use AuthorizationService::VALUE and
     * AuthorizationService::MESSAGE to access the fields of the array
     */
    public function mayShowEventDetail(Event $event);

    /**
     * Determines if the logged in user may delete the given event,
     * and if the given event may be deleted at all.
     * @param  Event  $event The event
     * @return array Array of two fields, stating if the event may be
     * deleted or not and why. Use AuthorizationService::VALUE and
     * AuthorizationService::MESSAGE to access the fields of the array
     */
    public function mayDelete(Event $event);

    /**
     * Determins if the user may download data from the Event page
     * @return bool True, if the user may download.
     */
    public function mayDownloadFromEvent();

    public function mayCopyEvent();

    /**
     * Tells if current logged in user is member of the association.
     * @return boolean true, if the user is member of the association.
     */
    public function isAssociationMember();

    public function maySeeAllEvents();

    public function maySeeUserPage();

    /**
     * Checks if the logged in user may send invitations for the given event.
     * @param  AppBundle\Entity\Event $event The event.
     * @return boolean Returns true, if user may send invitation mails.
     */
    public function maySendInvitation($event);

    /**
     * Checks if the logged in user may enroll to the given event.
     * @param  Event  $event The event.
     * @return boolean        Returns true, if the user may enroll to the event
     */
    public function mayEnroll(Event $event);

    /**
     * Checks if the logged in user may change commitments of the given event.
     * @param  Event  $event
     * @return boolean
     */
    public function mayEditOrDeleteCommitments(Event $event);

    /**
     * Checks if the logged in user may change or delete the given commitment.
     * @param  Commitment $commitment
     * @return boolean
     */
    public function mayEditOrDeleteCommitment(Commitment $commitment);

    /**
     * You may see departments if you are an admin.
     * @param  Event  $event
     * @return boolean
     */
    public function mayShowDepartmentsOfEvent(Event $event);

    /**
     * You may see departments if you are an admin.
     * @param  Event  $event
     * @return boolean
     */
    public function mayShowQuestionsOfEvent(Event $event);

    /**
     * you can edit questions if you are admin
     * @param  Question $question
     * @return boolean
     */
    public function mayEditQuestion(Question $question);

    /**
     * You can add questions to this event if you are admin
     * @param  Event $event
     * @return boolean
     */
    public function mayAddQuestion(Event $event);

    // TODO: add comment, use naming conventions.
    public function MayChangeSettings();

    /**
     * returns true if the logged in user may download volunteer data of the given department.
     * @param  Department $department
     * @return boolean
     */
    public function maySeeCommitments(Department $department);
}
