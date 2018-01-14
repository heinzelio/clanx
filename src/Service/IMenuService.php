<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Entity\Event;

interface IMenuService
{

    /**
     * Gets the menu item to get to the home page.
     * @return array Returns an array of values which are used in the view model.
     */
    public function getHomeMenu();

    /**
     * Gets the menu item to get to an event detail page.
     * @return array Returns an array of values which are used in the view model.
     */
    public function getEventMenu(Event $event);

    /**
     * Gets the menu item to get to the event index page.
     * @return array Returns an array of values which are used in the view model.
     */
    public function getInfoMenu();

    /**
     * Gets the menu item to get to the event index page.
     * @return array Returns an array of values which are used in the view model.
     */
    public function getEventIndexMenu();

    /**
     * Gets the menu item to get to the user index page.
     * @return array Returns an array of values which are used in the view model.
     * Returns null, if the logged in user may not see the users page.
     */
    public function getUserIndexMenu();
}

?>
