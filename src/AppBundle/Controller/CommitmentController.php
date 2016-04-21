<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Commitment;
use AppBundle\Form\CommitmentType;

/**
 * Commitment controller.
 *
 * @Route("/commitment")
 */
class CommitmentController extends Controller
{
    /**
     * Lists all Commitment entities.
     *
     * @Route("/", name="commitment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $commitments = $em->getRepository('AppBundle:Commitment')->findAll();

        return $this->render('commitment/index.html.twig', array(
            'commitments' => $commitments,
        ));
    }

    /**
     * Creates a new Commitment entity.
     *
     * @Route("/new", name="commitment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $commitment = new Commitment();
        $form = $this->createForm('AppBundle\Form\CommitmentType', $commitment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commitment);
            $em->flush();

            return $this->redirectToRoute('commitment_show', array('id' => $commitment->getId()));
        }

        return $this->render('commitment/new.html.twig', array(
            'commitment' => $commitment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Commitment entity.
     *
     * @Route("/{id}", name="commitment_show")
     * @Method("GET")
     */
    public function showAction(Commitment $commitment)
    {
        $deleteForm = $this->createDeleteForm($commitment);

        return $this->render('commitment/show.html.twig', array(
            'commitment' => $commitment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Commitment entity.
     *
     * @Route("/{id}/edit", name="commitment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Commitment $commitment)
    {
        $deleteForm = $this->createDeleteForm($commitment);
        $editForm = $this->createForm('AppBundle\Form\CommitmentType', $commitment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commitment);
            $em->flush();

            return $this->redirectToRoute('commitment_edit', array('id' => $commitment->getId()));
        }

        return $this->render('commitment/edit.html.twig', array(
            'commitment' => $commitment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Commitment entity.
     *
     * @Route("/{id}", name="commitment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Commitment $commitment)
    {
        $form = $this->createDeleteForm($commitment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($commitment);
            $em->flush();
        }

        return $this->redirectToRoute('commitment_index');
    }

    /**
     * Creates a form to delete a Commitment entity.
     *
     * @param Commitment $commitment The Commitment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Commitment $commitment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('commitment_delete', array('id' => $commitment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
