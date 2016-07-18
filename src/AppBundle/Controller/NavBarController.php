<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Event');
        // get all sticky events that are in the future or the very near past (1 week or so)
        $oneWeekInterval = new \DateInterval('P7D');
        $oneWeekInterval->invert=1; // negative interval. one week back.
        $aWeekAgo = new \DateTime();
        $aWeekAgo->add($oneWeekInterval);
        $query = $repository->createQueryBuilder('e')
            ->where('e.sticky = 1 AND e.date > :aWeekAgo')
            ->setParameter('aWeekAgo', $aWeekAgo)
            ->getQuery();

        $stickyEvents = $query->getResult();

        $homeItem = $this->createItem(
            'Home',
            'fa fa-home',
            'dashboard_index');

        $items = array($homeItem);
        foreach ($stickyEvents as $event) {
            $stickyItem = $this->createItem(
                $event->getName(),
                null,
                'event_show',
                'id',
                $event->getId(),
                true); // "hot" items are visually emphasized
            array_push($items,$stickyItem);
        }

        $eventItem = $this->createItem(
            'Events',
            'fa fa-calendar',
            'event_index');
        array_push($items,$eventItem);

        if($this->isGranted('ROLE_ADMIN')){
                    $userItem = $this->createItem(
                        'Users',
                        'fa fa-users',
                        'user_index');
                    array_push($items,$userItem);
        }

        $infoItem = $this->createItem(
            'Info',
            'fa fa-info',
            'info_index');
        array_push($items,$infoItem);

        return $this->render('snippets/navbar.html.twig', array(
            'navbarItems' => $items,
        ));
    }

    private function createItem(
        $name,
        $icon,
        $routename,
        $argumentName=null,
        $argumentValue=null,
        $hot=false)
    {
        return array(
            'text' => $name,
            'icon' => $icon,
            'routename' => $routename,
            'hasArguments' => $argumentName!=null,
            // later this may be a collection!
            'arguments' => array($argumentName => $argumentValue ),
            'hot' => $hot,
        );
    }
}

 ?>
