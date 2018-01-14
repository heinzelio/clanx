<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\TranslatorInterface;
use App\Entity\Event;
use App\Entity\User;
use App\ViewModel\User\UserIndex;

class UserService implements IUserService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * The repository for the User entity
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repo;

    public function __construct(
        EntityManager $em,
        TranslatorInterface $trans
    )
    {
        $this->entityManager = $em;
        $this->trans = $trans;
        $this->repo = $em->getRepository('AppBundle:User');
    }

    /**
     * Gets all users that are association members
     * @return Returns an array of users.
     */
    public function getAllAssociationMembers()
    {
        $qb = $this->repo->createQueryBuilder('u');

        $assocFilter = $qb->expr()->eq('u.isAssociationMember',1);

        $qb->where($assocFilter)
            ->orderBy('u.surname')
            ->addOrderBy('u.forename');

        return $qb->getQuery()->getResult();
    }

    /**
     * Gets an array of all Users.
     * @return Returns an array of User entities.
     */
    public function getAllUsers()
    {
        return $this->repo->findAll();
    }

    /**
     * Builds and returns the view model for the user index
     * @param  Translator $trans
     * @return UserIndex
     */
    public function getIndexViewModel()
    {
        $userIndex = new UserIndex($this->trans);
        $userIndex->addUserRange($this->getAllUsers());
        return $userIndex;
    }

    public function handleUserIndexFormSubmit(UserIndex $vm)
    {
        // TODO: Make this SOLID!
        $functionEachChecked = function() {};
        $functionAfter = function() {};
        switch ($vm->getBulk()->getAction()) {
            case 'toggle_member':
                $functionEachChecked = function($user) {
                    $user->setIsAssociationMember(!$user->getIsAssociationMember());
                };
                $functionAfter = function($em){
                    $flashMessages = array( );
                    try {
                        $em->flush();
                        array_push($flashMessages, array('success', 'flash.successfully_saved'));
                    } catch (Exception $e) {
                        array_push($flashMessages, array('danger', 'flash.save_failed'));
                        array_push($flashMessages, array('info', '$e->getMessage()'));
                    }
                    return $flashMessages;
                };
                break;
            case 'toggle_regular':
                $functionEachChecked = function($user) {
                    $user->setIsRegular(!$user->getIsRegular());
                };
                $functionAfter = function($em){
                    $flashMessages = array( );
                    try {
                        $em->flush();
                        array_push($flashMessages, array('success', 'flash.successfully_saved'));
                    } catch (Exception $e) {
                        array_push($flashMessages, array('danger', 'flash.save_failed'));
                        array_push($flashMessages, array('info', '$e->getMessage()'));
                    }
                    return $flashMessages;
                };
                break;
            case 'send_mail':
            default:
                $functionAfter = function () {
                    return array(array('info', 'flash.not_yet_implemented'));
                };
        }
        $recordAffected = false;
        foreach ($vm->getBulk()->getEntries() as $bulkEntry) {
            if($bulkEntry->getChecked()){
                $recordAffected = true;
                $userIndexEntry = $vm->getUsers()[$bulkEntry->getId()];
                $functionEachChecked($userIndexEntry->getUser());
            }
        }
        if (!$recordAffected) {
            return array(array('info', 'flash.no_record_affected'));
        }
        else {
            return $functionAfter($this->entityManager);
        }
    }

}
