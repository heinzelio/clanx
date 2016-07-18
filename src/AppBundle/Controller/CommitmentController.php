<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Form\CommitmentType;

/**
 * Commitment controller.
 *
 * @Route("/commitment")
 */
class CommitmentController extends Controller
{
    /**
     * Displays a form to edit an existing Commitment entity.
     *
     * @Route("/{id}/edit", name="commitment_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, Commitment $commitment)
    {
        $event = $commitment->getEvent();

        if($event->getLocked()){
            $this->get('session')->getFlashBag()->add('danger', "Event gesperrt. Ändern nicht mehr möglich.");
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        $em = $this->getDoctrine()->getManager();
        $deleteForm = $this->createDeleteForm($commitment);

        //TODO: replacy dummy
        $mayDelete = true;

        $options = array('departmentChoices' => $event->getFreeDepartments());
        $editForm = $this->createForm('AppBundle\Form\CommitmentType', $commitment, $options);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($commitment);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Änderung gespeichert.");
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('commitment/edit.html.twig', array(
            'commitment' => $commitment,
            'department' => $commitment->getDepartment(),
            'event' => $event,
            'may_delete' => $mayDelete,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Commitment entity.
     *
     * @Route("/{id}", name="commitment_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, Commitment $commitment)
    {
        $accessViolation = $this->getUser()->getID() != $commitment->getUser()->getId();
        if($accessViolation){
            $this->get('session')->getFlashBag()->add('danger', "Du darfst diesen Datensatz nicht löschen.");
            return $this->redirectToRoute('event_show', array('id' => $commitment->getEvent()->getId()));
        }

        if($commitment->getEvent()->getLocked())
        {
            $this->get('session')->getFlashBag()->add('danger', "Event gesperrt. Löschen nicht mehr möglich.");
            return $this->redirectToRoute('event_show', array('id' => $commitment->getEvent()->getId()));
        }

        $form = $this->createDeleteForm($commitment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commitment);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Datensatz wurde gelöscht.");
        }

        return $this->redirectToRoute('event_show', array('id'=>$commitment->getEvent()->getId()));
    }

    /**
     * Creates a form to delete a commitment entity.
     *
     * @param Commitment $commitment The Commitment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commitment $commitment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commitment_delete', array(
                'id' => $commitment->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
