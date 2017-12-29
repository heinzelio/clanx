<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Service\IEventService;

/**
 * Event controller.
 *
 * @Route("/event")
 */
class NavBarController extends Controller
{
    /**
     * Creates all the available NavBar items for the logged in user.
     *
     * @Route("/", name="navbar_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(IEventService $eventService)
    {
        $menuService = $this->get('app.menu');

        $items[] = $menuService->getHomeMenu();
        foreach ($eventService->getEventsForMenu() as $event) {
            $items[] = $menuService->getEventMenu($event);
        }
        $items[] = $menuService->getEventIndexMenu();
        $items[] = $menuService->getUserIndexMenu();
        $items[] = $menuService->getInfoMenu();

        return $this->render('snippets/navbar.html.twig', array(
            'navbarItems' => $items,
        ));
    }
}

 ?>
