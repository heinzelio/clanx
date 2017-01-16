<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\CommitmentViewModel;
use AppBundle\Form\Commitment\CommitmentType;
use AppBundle\Form\Commitment\TextQuestionViewModel;

/**
 * Partial event commitment controller.
 *
 * @Route("/event/commitment")
 */
class EventCommitmentController extends Controller
{
    /**
     * Shows the enroll view.
     *
     * @Route("/{id}", name="event_commitment_show")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Request $request, Event $event)
    {
        $authService = $this->get('app.auth');
        if(!$authService->mayEnroll($event)){
            return $this->render('event/may_not_enroll.html.twig');
        }

        $eventService = $this->get('app.event');
        $formVM = $eventService->getCommitmentFormViewModel($event);

        $enrollForm = $this->getEnrollForm($formVM);

        $enrollForm->handleRequest($request);
        if ($enrollForm->isSubmitted() && $enrollForm->isValid()) {
            // TODO: get a service, send the model to the service and redirect to
            // event show.
             $this->get('session')->getFlashBag()->add('success', "Boing.");

            return $this->redirectToRoute('event_show', array(
                'id' => $event->getId(),
            ));
        }

        return $this->render('event/enroll.html.twig', array(
            'enroll_form' => $enrollForm->createView(),
            'save_tooltip' => 'save and send notification. or not. who knows.',
            'btn_save_text' => 'speicheln',
            'event_id' => $event->getId(),
        ));
    }

    /**
     * @param  CommitmentViewModel $vm
     * @return FormType
     */
    private function getEnrollForm(CommitmentViewModel $vm)
    {
        $options = array(
            CommitmentType::DEPARTMENT_CHOICES_KEY => $vm->getDepartments(),
            CommitmentType::USE_DEPARTMENTS_KEY => $vm->hasDepartments(),
            CommitmentType::USE_VOLUNTEER_NOTIFICATION_KEY => true,
        );

        $form = $this->createForm('AppBundle\Form\Commitment\CommitmentType', $vm, $options);

        foreach ($vm->getQuestions() as $q) {
            $attributes = array();
            $attributes = $q->fillAttributes($attributes);

            $form->add($q->getFormFieldName(), $q->getFormType(), $attributes);
        }
        return $form;
    }
}
