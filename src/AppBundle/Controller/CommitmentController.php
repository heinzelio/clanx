<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        $operator = $this->getUser();
        $department = $commitment->getDepartment();
        $event = $commitment->getEvent();
        if (!$this->mayEditOrDelete($commitment))
        {
            $this->get('session')->getFlashBag()->add('warning', "Eintrag kann nicht geändert oder gelöscht werden.");
            return $this->redirectToRoute('department_show', array('id' => $department->getId(),'event_id'=>$event->getId()));
        }

        $deleteForm = $this->createDeleteForm($commitment);

        $options = array(
            'departmentChoices' => $event->getDepartments()
        );
        $editForm = $this->createForm('AppBundle\Form\CommitmentType', $commitment, $options);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $message = $editForm->get('message')->getData();

            $this->sendMail($message,$commitment,$operator);

            $em = $this->getDoctrine()->getManager();
            $em->persist($commitment);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Änderung gespeichert, ".$commitment->getUser()." wurde benachrichtigt.");
            return $this->redirectToRoute('department_show', array('id' => $department->getId(),'event_id'=>$event->getId()));
        }

        return $this->render('commitment/edit.html.twig', array(
            'commitment' => $commitment,
            'department' => $commitment->getDepartment(),
            'volunteer' => $commitment->getUser(),
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    private function mayEditOrDelete($commitment)
    {
        $operator = $this->getUser();
        $department = $commitment->getDepartment();
        $event = $commitment->getEvent();

        return (
                     $operator->isChiefOf($department)
                    ||  $operator->isDeputyOf($department)
                    ||  $this->isGranted('ROLE_ADMIN')
                )
                && !$event->getLocked()
                && $event->isFuture();
    }

    private function sendMail($text,$commitment,$operator)
    {
        $event = $commitment->getDepartment()->getEvent();
        $volunteer = $commitment->getUser();
        $message = \Swift_Message::newInstance();
        $message->setSubject('Dein Einsatz am '.(string)$event.' - Einsatzänderung!')
            ->setFrom($operator->getEmail())
            ->setTo($volunteer->getEmail())
            ->setBody(
                $this->renderView(
                    // app/Resources/views/emails/commitmentConfirmation.html.twig
                    'emails/commitment_changed.html.twig',
                    array(
                        'text' => $text,
                        'event' => $commitment->getDepartment()->getEvent(),
                        'operator' => $operator,
                        'volunteer' => $volunteer,
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    // app/Resources/views/emails/commitmentConfirmation.txt.twig
                    'emails/commitment_changed.txt.twig',
                    array(
                        'text' => $text,
                        'event' => $commitment->getDepartment()->getEvent(),
                        'operator' => $operator,
                        'volunteer' => $volunteer,
                    )
                ),
                'text/plain'
            )
        ;
        return $this->get('mailer')->send($message);
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
        $department = $commitment->getDepartment();
        $event = $department->getEvent();
        if (!$this->mayEditOrDelete($commitment))
        {
            $this->get('session')->getFlashBag()->add('warning', "Eintrag kann nicht geändert oder gelöscht werden.");
            return $this->redirectToRoute('department_show', array('id' => $department->getId(),'event_id'=>$event->getId()));
        }

        $form = $this->createDeleteForm($commitment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $message = $form->get('message')->getData();
            $this->sendMail($message,$commitment,$this->getUser());
            $volunteer = $commitment->getUser();
            $em = $this->getDoctrine()->getManager();
            $em->remove($commitment);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Dieser Einsatz wurde gelöscht. ".$volunteer." wurde benachrichtigt.");
        }

        return $this->redirectToRoute('department_show', array('id' => $department->getId(),'event_id'=>$event->getId()));
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
            ->add('message', HiddenType::class, array(
                'attr' => array('class'=>'clx-commitment-delete-message'), )) // on btn click, data will be copied from the commitment form
            ->setAction($this->generateUrl('commitment_delete', array(
                'id' => $commitment->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
