<?php

namespace AppBundle\ViewModel\User;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormView;
use AppBundle\Entity\User;
use AppBundle\Entity\Bulk;
use AppBundle\Entity\BulkEntry;
/**
 * Contains all that the has to be shown on the User Index page
 */
class UserIndex
{
    /**
     * @var UserIndexEntry[]
     */
    private $users = array();

    /**
     * @var string
     */
    private $title;

    /**
     * @var Bulk
     */
    private $bulk;

    /**
     * @var FormView
     */
    private $bulkFormView;

    function __construct(TranslatorInterface  $trans)
    {
        $this->bulk = new Bulk();
        $this->bulk->addActionChoice('toggle_member', $trans->trans('lbl.toggle_member', array(),'user'));
        $this->bulk->addActionChoice('toggle_regular', $trans->trans('lbl.toggle_regular', array(),'user'));
        $this->bulk->addActionChoice('send_mail', $trans->trans('lbl.send_mail', array(),'user'));

        $this->title = $trans->trans('title.user_index', array(),'user');
    }

    /**
     * Get the value of Users
     *
     * @return UserIndexEntry[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set the value of Users
     *
     * @param UserIndexEntry[] users
     *
     * @return self
     */
    public function setUsers($users)
    {
        $this->users = $users;
        return $this;
    }

    /**
     * Adds one user to the array of users
     *
     * @param User user
     *
     * @return self
     */
    public function addUser($user)
    {
        $userId = $user->getId();
        $this->users[$userId] = new UserIndexEntry($user);
        $entry = new BulkEntry();
        $entry->setId($userId);
        $this->bulk->addEntry($entry);
        return $this;
    }

    /**
     * Adds users to the array of users
     *
     * @param User[] users
     *
     * @return self
     */
    public function addUserRange($users)
    {
        foreach ($users as $user) {
            $this->addUser($user);
        }
        return $this;
    }

    /**
     * Get the value of Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of Title
     *
     * @param string title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }


    /**
     * Get the value of Bulk
     *
     * @return Bulk
     */
    public function getBulk()
    {
        return $this->bulk;
    }

    /**
     * Get the value of Bulk Form View
     *
     * @return FormView
     */
    public function getBulkFormView()
    {
        return $this->bulkFormView;
    }

    /**
     * Set the value of Bulk Form View
     *
     * @param FormView bulkFormView
     *
     * @return self
     */
    public function setBulkFormView(FormView $bulkFormView)
    {
        $this->bulkFormView = $bulkFormView;
        return $this;
    }
}
