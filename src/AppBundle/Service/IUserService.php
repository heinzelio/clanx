<?php
namespace AppBundle\Service;

use AppBundle\ViewModel\User\UserIndex;

interface IUserService
{
    /**
     * Gets all users that are association members
     * @return Returns an array of users.
     */
    public function getAllAssociationMembers();

    /**
     * Gets an array of all Users.
     * @return Returns an array of User entities.
     */
    public function getAllUsers();

    /**
     * Builds and returns the view model for the user index
     * @param  Translator $trans
     * @return UserIndex
     */
    public function getIndexViewModel();

    public function handleUserIndexFormSubmit(UserIndex $vm);
}
