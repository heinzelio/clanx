<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Shift;
use AppBundle\Entity\Department;
use AppBundle\Form\ShiftType;

/**
 * Shift controller.
 *
 * @Route("/shift")
 */
class ShiftController extends Controller
{
    /**
     * Lists all Shift entities.
     *
     * @Route("/of/department/{department_id}", name="shift_index_of_department")
     * @Method("GET")
     * @ParamConverter("department", class="AppBundle:Department", options={"id" = "department_id"})
     * @Security("has_role('ROLE_OK') or has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request, Department $department)
    {
        $shifts = $department->getShifts();

        return $this->render('shift/index.html.twig', array(
            'shifts' => $shifts,
            'department' => $department,
        ));
    }

    /**
     * Creates a new Shift entity.
     *
     * @Route("/new/for/department/{department_id}", name="shift_new_for_department")
     * @Method({"GET", "POST"})
     * @ParamConverter("department", class="AppBundle:Department", options={"id" = "department_id"})
     * @Security("has_role('ROLE_OK') or has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request, Department $department)
    {
        $otherShifts = $department->getShifts()->toArray();
        $recentShift = end($otherShifts);

        $shift = new Shift();
        $shift->setDepartment($department);
        if($recentShift && $recentShift->getEnd())
        {
            $shift->setStart($recentShift->getEnd());
        }else if($recentShift)
        {
            $shift->setStart($recentShift->getStart());
        }else{
            $shift->setStart($department->getEvent()->getDate());
        }
        $shift->setEnd($shift->getStart());
        $form = $this->createForm('AppBundle\Form\ShiftType', $shift);
        $form->handleRequest($request);

        // Validation is in Shift.validate()
        // Configured by annotation "Assert/Callback"

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shift);
            $em->flush();

            return $this->redirectToRoute('shift_index_of_department', array('department_id' => $department->getId()));
        }

        return $this->render('shift/new.html.twig', array(
            'shift' => $shift,
            'department' => $department,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Shift entity.
     *
     * @Route("/{id}/edit", name="shift_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_OK') or has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Shift $shift)
    {
        $deleteForm = $this->createDeleteForm($shift);
        $editForm = $this->createForm('AppBundle\Form\ShiftType', $shift);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($shift);
            $em->flush();

            return $this->redirectToRoute('shift_edit', array('id' => $shift->getId()));
        }

        return $this->render('shift/edit.html.twig', array(
            'shift' => $shift,
            'department' => $shift->getDepartment(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Shift entity.
     *
     * @Route("/{id}", name="shift_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_OK') or has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Shift $shift)
    {
        $form = $this->createDeleteForm($shift);
        $form->handleRequest($request);
        $department = $shift->getDepartment();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shift);
            $em->flush();
        }

        return $this->redirectToRoute('shift_index_of_department', array('department_id' => $department->getId()));
    }

    /**
     * Creates a form to delete a Shift entity.
     *
     * @param Shift $shift The Shift entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Shift $shift)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('shift_delete', array('id' => $shift->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
