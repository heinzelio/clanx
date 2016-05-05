<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Department;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Form\DepartmentType;

/**
 * Department controller.
 *
 * @Route("/department")
 */
class DepartmentController extends Controller
{
    /**
     * Lists all Department entities.
     *
     * @Route("/", name="department_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $departments = $em->getRepository('AppBundle:Department')->findAll();

        return $this->render('department/index.html.twig', array(
            'departments' => $departments,
        ));
    }

    /**
     * Creates a new Department entity.
     *
     * @Route("/new/for/event/{event_id}", name="department_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function newAction(Request $request, Event $event)
    {
        $department = new Department();
        $form = $this->createForm('AppBundle\Form\DepartmentType', $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $department->setEvent($event);
            $em->persist($department);
            $em->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('department/new.html.twig', array(
            'department' => $department,
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Department entity.
     *
     * @Route("/{id}/of/event/{event_id}", name="department_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function showAction(Department $department,Event $event)
    {
        $deleteForm = $this->createDeleteForm($department,$event);
        $em = $this->getDoctrine()->getManager();
        $commRepo = $em->getRepository('AppBundle:Commitment');
        $shiftRepo = $em->getRepository('AppBundle:Shift');

        $qb = $em->createQueryBuilder();
        $qb->select('count(shift.id)')
        ->from('AppBundle:Shift','shift')
        ->where('shift.department = :dpt')
        ->setParameter('dpt', $department);
        $countShift = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(cmt.id)')
        ->from('AppBundle:Commitment','cmt')
        ->where('cmt.department = :dpt')
        ->setParameter('dpt', $department);
        $countCommitment = $qb->getQuery()->getSingleScalarResult();

        $user = $this->getUser(); // NOPE!!!

        $userRepo = $em->getRepository('AppBundle:User');
        $commitments = $commRepo->findByDepartment($department);
        $volunteers = array();
        foreach ($commitments as $cmt) {
            array_push($volunteers,$cmt->getUser());
        }

        $mayDelete = $this->isGranted('ROLE_ADMIN');
        $mayDelete = $mayDelete && $countShift == 0;
        $mayDelete = $mayDelete && $countCommitment == 0;

        return $this->render('department/show.html.twig', array(
            'department' => $department,
            'event' => $event,
            'mayDelete' => $mayDelete,
            'delete_form' => $deleteForm->createView(),
            'volunteers' => $volunteers,
        ));
    }

    /**
     * Displays a form to edit an existing Department entity.
     *
     * @Route("/{id}/of/event/{event_id}/edit", name="department_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function editAction(Request $request, Department $department, Event $event)
    {
        $deleteForm = $this->createDeleteForm($department,$event);
        $editForm = $this->createForm('AppBundle\Form\DepartmentType', $department);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "'".$department->getName()."' gespeichert.");

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('department/edit.html.twig', array(
            'department' => $department,
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Department entity.
     *
     * @Route("/{id}/of/event/{event_id}", name="department_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function deleteAction(Request $request, Department $department, Event $event)
    {
        $form = $this->createDeleteForm($department,$event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($department);
            $em->flush();
            //TODO: Add msg to flashbag.
        }

        return $this->redirectToRoute('event_edit', array('id'=>$event->getId()));
    }

    /**
     * Creates a form to delete a Department entity.
     *
     * @param Department $department The Department entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Department $department, Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('department_delete', array(
                'id' => $department->getId(),
                'event_id'=>$event->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
