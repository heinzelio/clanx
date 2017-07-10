<?php

namespace AppBundle\ViewModel\User;

use AppBundle\Entity\User;

/**
 * Contains data to show on one line on the user Index
 */
class UserIndexEntry
{
    /**
     * The user entity
     * @var User $user
     */
    protected $user;

    function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the value of The user entity
     *
     * @return User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of The user entity
     *
     * @param User $user user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the value of Role String
     *
     * @return string
     */
    public function getRoleString()
    {
        $roleString='';
        $ctRoles = $this->user->getRoles();
        if(in_array('ROLE_ADMIN',$ctRoles)){$roleString = $roleString."Adm, ";}
        if(in_array('ROLE_SUPER_ADMIN',$ctRoles)){$roleString = $roleString."SA, ";}
        if(in_array('ROLE_OK',$ctRoles)){$roleString = $roleString."OK, ";}
        return $roleString;
    }

}
