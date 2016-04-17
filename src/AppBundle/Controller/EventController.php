<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType;

/**
 * Event controller.
 *
 * @Route("/event")
 */
class EventController extends Controller
{
    /**
     * Lists all Event entities.
     *
     * @Route("/", name="event_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('AppBundle:Event');

        $queryUpcoming = $repository->createQueryBuilder('e')
            ->where('e.date >= :today')
            ->setParameter('today', new \DateTime("now"))
            ->orderBy('e.date', 'ASC')
            ->getQuery();

        $queryPassed = $repository->createQueryBuilder('e')
            ->where('e.date < :today')
            ->setParameter('today', new \DateTime("now"))
            ->orderBy('e.date', 'DESC')
            ->getQuery();

        $upcoming = $queryUpcoming->getResult();
        $passed = $queryPassed->getResult();

        return $this->render('event/index.html.twig', array(
            'upcomingEvents' => $upcoming,
            'passedEvents' => $passed,
        ));
    }

    /**
     * Creates a new Event entity.
     *
     * @Route("/new", name="event_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $event = new Event();
        $form = $this->createForm('AppBundle\Form\EventType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "'".$event->getName()."' gespeichert");
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);

        // todo: find the values for these:
        // maybe consider the use of "voters"
        // https://symfony.com/doc/current/cookbook/security/voters.html#how-to-use-the-voter-in-a-controller

        $em = $this->getDoctrine()->getManager();
        $commitments = $em->getRepository('AppBundle:Commitment');

        $enrolledCount = $commitments->countFor($event);

        $isEnrolled = $commitments->existsFor( $this->getUser() ,$event);

        $mayEnroll = !$isEnrolled && $event->enrollmentPossible();

        $user = $this->getUser();

        $mayEdit = $this->isGranted('ROLE_ADMIN') && $event->mayEdit();
        $mayDelete = $this->isGranted('ROLE_SUPER_ADMIN') && $event->mayDelete();

        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'mayEnroll' => $mayEnroll,
            'enrolledCount' => $enrolledCount,
            'isEnrolled' => $isEnrolled,
            'mayEdit' => $mayEdit,
            'mayDelete' => $mayDelete,
        ));
    }

    /**
     * Displays a form to edit an existing Event entity.
     *
     * @Route("/{id}/edit", name="event_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Event $event)
    {
        $deleteForm = $this->createDeleteForm($event);
        $editForm = $this->createForm('AppBundle\Form\EventType', $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Event entity.
     *
     * @Route("/{id}", name="event_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Event $event)
    {
        $form = $this->createDeleteForm($event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($event);
            $em->flush();
        }

        return $this->redirectToRoute('event_index');
    }

    /**
     * Creates a form to delete a Event entity.
     *
     * @param Event $event The Event entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('event_delete', array('id' => $event->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/enroll/{id}", name="event_enroll")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function enrollAction(Event $event)
    {
        // TODO
        // show the enroll form

        // this basically redirects to "commitment_new", with all necessary parameters.

        // after enrollment, redirect back to "event_show"
    }
}
