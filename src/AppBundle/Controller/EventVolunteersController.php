<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;

/**
 * Event controller.
 *
 * @Route("/event/volunteers")
 */
class EventVolunteersController extends Controller
{
    /**
     * Shows basic volunteer information.
     *
     * @Route("/{id}", name="event_volunteers_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Event $event)
    {
        if($this->isGranted('ROLE_ADMIN')){
            return $this->renderAdminView($event);
        }
        if($this->isChiefOfAnyDepartment($event))
        {
            return $this->renderChiefView($event);
        }
        $deputiesDepartments = $this->getDeputiesDepartments($event);
        if($deputiesDepartments)
        {
            return $this->renderDeputyView($event,$deputiesDepartments);
        }
        // regular users do not see anything.
        return new Response('');
    }

    private function isChiefOfAnyDepartment(Event $event)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Department');
        foreach ($repo->findByEvent($event) as $dept)
        {
            if($user->IsChiefOf($dept)){
                return true;
            }
        }
        return false;
    }

    private function getDeputiesDepartments(Event $event)
    {

        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Department');

        $departments = null;
        $user = $this->getUser();
        foreach ($repo->findByEvent($event) as $dept)
        {
            if(!$departments)
            {
                $departments=array();
            }

            if($user->isDeputyOf($dept)){
                array_push($departments, $dept);
            }
        }
        return $departments;
    }

    private function renderAdminView(Event $event)
    {
        // nothing special so far.
        return $this->renderChiefView($event);
    }

    private function renderChiefView(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $deptartmentRepo = $em->getRepository('AppBundle:Department');
        $commitmentRepo = $em->getRepository('AppBundle:Commitment');
        $viewDepartments = array();
        foreach ($deptartmentRepo->findByEvent($event) as $department) {
            $commitments = $commitmentRepo->findByDepartment($department);
            $users = array();
            foreach ($commitments as $cmt) {
                array_push($users,(string)$cmt->getUser());
            }
            $newItem = array(
                'id' => $department->getId(),
                'highlighted' => $this->getUser()->isChiefOf($department),
                'name' => $department->getName(),
                'usercount' => count($commitments),
                'users' => $users
            );
            array_push($viewDepartments,$newItem);
        }

        return $this->render('event/volunteers.html.twig', array(
            'departments' => $viewDepartments,
            'event' => $event,
        ));
    }

    private function renderDeputyView(Event $event, array $departments)
    {
        $em = $this->getDoctrine()->getManager();
        $commitmentRepo = $em->getRepository('AppBundle:Commitment');
        $viewDepartments = array();
        foreach ($departments as $department) {
            $commitments = $commitmentRepo->findByDepartment($department);
            $users = array();
            foreach ($commitments as $cmt) {
                array_push($users,(string)$cmt->getUser());
            }
            $newItem = array(
                'id' => $department->getId(),
                'highlighted' => false,
                'name' => $department->getName(),
                'usercount' => count($commitments),
                'users' => $users
            );
            array_push($viewDepartments,$newItem);
        }

        return $this->render('event/volunteers.html.twig', array(
            'departments' => $viewDepartments,
            'event' => $event,
        ));
    }
}
