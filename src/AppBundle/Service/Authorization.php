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

class Authorization
{
    /**
     * Constant key to access the value field in the return
     * array of the method mayDelete(Event)
     * @var string
     */
    const VALUE = 'Value';

    /**
     * Constant key to access the message field in the return
     * array of the method mayDelete(Event)
     * @var string
     */
    const MESSAGE = 'Message';

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    protected $tokenStorage;
    protected $authorizationChecker;

    /**
     * @var AppBundle\Entity\User
     */
    protected $user;

    public function __construct(
        EntityManager $em,
        TokenStorage $ts,
        AuthorizationChecker $autch
    )
    {
        $this->entityManager = $em;
        $this->securityContext = $ts;
        $this->authorizationChecker = $autch;
        $this->user = $ts->getToken()->getUser();
    }

    /**
     * Shortcut for the call of authChecker.isGranted() method.
     * @param  string  $role the user role
     * @return boolean Returns true, if the role is granted.
     */
    private function isGranted($role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    /**
     * Checks if the logged in user is super admin
     * @return boolean
     */
    private function isSA()
    {
        return ($this->isGranted('ROLE_SUPER_ADMIN'));
    }

    /**
     * Gets the current logged in user
     * @return AppBundle\Entity\User The user.
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Determines if the logged in user may send a mass email to all
     * volunteers of an event
     * @return boolean True if user may send mass email.
     */
    public function maySendEventMassMail()
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * Determines if the logged in user may edit the given event
     * @param  Event $event The event
     * @return boolean True, if the event may be edited.
     */
    public function mayEditEvent(Event $event=null)
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * Determines if the event detail may be shown to the logged in user
     * and returns an array if so or not and a message why.
     * @param  Event  $event The event.
     * @return array Array of two fields, stating if the event may be
     * shown or not and why. Use Authorization::VALUE and
     * Authorization::MESSAGE to access the fields of the array
     */
    public function mayShowEventDetail(Event $event)
    {
        $returnValue = array();

        if ($this->maySeeAllEvents()) {
            $returnValue[Authorization::VALUE] = true;
            $returnValue[Authorization::MESSAGE] = 'Der Benutzer darf alle Events sehen.';
        }
        else if ($event->getIsForAssociationMembers() && (!$this->isAssociationMember()))
        {
            $returnValue[Authorization::VALUE] = false;
            $returnValue[Authorization::MESSAGE] = 'Dieser Event ist nur für Vereinsmitglieder sichtbar, der Benutzer ist aber nicht Vereinsmitglied.';
        }
        else
        {
            $returnValue[Authorization::VALUE] = true;
            $returnValue[Authorization::MESSAGE] = 'Der Benutzer darf diesen Event sehen.';
        }
        return $returnValue;
    }

    /**
     * Determines if the logged in user may delete the given event,
     * and if the given event may be deleted at all.
     * @param  Event  $event The event
     * @return array Array of two fields, stating if the event may be
     * deleted or not and why. Use Authorization::VALUE and
     * Authorization::MESSAGE to access the fields of the array
     */
    public function mayDelete(Event $event)
    {
        $returnValue = array();
        // check event locked:
        if($event->getLocked()==1){
            $returnValue[Authorization::VALUE] = false;
            $returnValue[Authorization::MESSAGE] = 'Event "'.$event.'" ist gesperrt und kann nicht gelöscht werden.';
            return $returnValue;
        }

        foreach ($event->getDepartments() as $department ) {
            $commitments = $department->getCommitments();
            if($commitments && $commitments->count()){
                $returnValue[Authorization::VALUE] = false;
                $returnValue[Authorization::MESSAGE] = 'Event "'.$event.'" hat bereits Hölfer und kann nicht mehr gelöscht werden.';
                return $returnValue;
            }
        }

        //$user = $securityContext->getToken()->getUser();
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $returnValue[Authorization::VALUE] = false;
            $returnValue[Authorization::MESSAGE] = 'Nur Administratoren dürfen Events löschen.';
            return $returnValue;
        }

        $returnValue[Authorization::VALUE] = true;
        $returnValue[Authorization::MESSAGE] = 'OK';
        return $returnValue;
    }

    /**
     * Determins if the user may download data from the Event page
     * @return bool True, if the user may download.
     */
    public function mayDownloadFromEvent()
    {
        return $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_OK');
    }

    public function mayCopyEvent()
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * Tells if current logged in user is member of the association.
     * @return boolean true, if the user is member of the association.
     */
    public function isAssociationMember()
    {
        return $this->user->getIsAssociationMember();
    }

    public function maySeeAllEvents()
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    public function maySeeUserPage()
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if the logged in user may send invitations for the given event.
     * @param  AppBundle\Entity\Event $event The event.
     * @return boolean Returns true, if user may send invitation mails.
     */
    public function maySendInvitation($event)
    {
        if($this->isGranted('ROLE_ADMIN'))
        {
            return true;
        }
        return false;
    }

    /**
     * Checks if the logged in user may enroll to the given event.
     * @param  Event  $event The event.
     * @return boolean        Returns true, if the user may enroll to the event
     */
    public function mayEnroll(Event $event)
    {
        if ($event->getLocked()) {
            return false;
        }
        if ($event->getIsForAssociationMembers() && !$this->user->getIsAssociationMember()) {
            return false;
        }
        if ($event->getDate() < new \DateTime()) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the logged in user may change commitments of the given event.
     * @param  Event  $event
     * @return boolean
     */
    public function mayEditOrDeleteCommitments(Event $event)
    {
        if($this->isSA()) return true;

        return $this->isGranted('ROLE_ADMIN') && !$event->getLocked();
    }

    /**
     * Checks if the logged in user may change or delete the given commitment.
     * @param  Commitment $commitment
     * @return boolean
     */
    public function mayEditOrDeleteCommitment(Commitment $commitment)
    {
        if($this->isSA()) return true;

        $operator = $this->getUser();
        $department = $commitment->getDepartment();
        $event = $commitment->getEvent();

        // user must be either chief of deputy of the department,
        // or he must be at least admin.
        // Furthermore the event may not be locked.
        return (
                     $this->user->isChiefOf($department)
                    ||  $this->user->isDeputyOf($department)
                    ||  $this->isGranted('ROLE_ADMIN')
                )
                && !$event->getLocked();
    }

    /**
     * You may see departments if you are an admin.
     * @param  Event  $event
     * @return boolean
     */
    public function mayShowDepartmentsOfEvent(Event $event)
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * You may see departments if you are an admin.
     * @param  Event  $event
     * @return boolean
     */
    public function mayShowQuestionsOfEvent(Event $event)
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * you can edit questions if you are admin
     * @param  Question $question
     * @return boolean
     */
    public function mayEditQuestion(Question $question)
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * You can add questions to this event if you are admin
     * @param  Event $event
     * @return boolean
     */
    public function mayAddQuestion(Event $event)
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    // TODO: add comment, use naming conventions.
    public function MayChangeSettings()
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    /**
     * returns true if the logged in user may download volunteer data of the given department.
     * @param  Department $department
     * @return boolean
     */
    public function maySeeCommitments(Department $department)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $chiefUser= $department->getChiefUser();
            $deputyUser = $department->getDeputyUser();
            if ($this->user->getId() != $chiefUser->getId()
                &&
                $this->user->getId() != $deputyUser->getId()
            )
            {
                return false;
            }
        }
        return true;
    }
}
