<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\Translator;
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
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        if(!$authService->mayEnroll($event)){
            return $this->render('event/may_not_enroll.html.twig');
        }

        $eventService = $this->get('app.event');
        $formVM = $eventService->getCommitmentFormViewModel($event); //CommitmentViewModel

        $enrollForm = $this->getEnrollForm($formVM);

        $enrollForm->handleRequest($request);
        if ($enrollForm->isSubmitted() && $enrollForm->isValid()) {
            // TODO: get a service, send the model to the service and redirect to
            // event show.
            $commitmentService = $this->get('app.commitment');
            $commitment = $commitmentService->saveCommitment($event, $formVM);
            if ($commitment != null) {
                //TODO finish here
                $this->sendMail($commitment);
                $this->addFlash('success','flash.enroll_succeeded');
            } else {
                $this->addFlash('warning','flash.enroll_failed');
            }

            return $this->redirectToRoute('event_show', array(
                'id' => $event->getId(),
            ));
        }

        return $this->render('event/enroll.html.twig', array(
            'enroll_form' => $enrollForm->createView(),
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
            CommitmentType::USE_VOLUNTEER_NOTIFICATION_KEY => false,
        );

        $form = $this->createForm('AppBundle\Form\Commitment\CommitmentType', $vm, $options);

        foreach ($vm->getQuestions() as $q) {
            $attributes = array();
            $attributes = $q->fillAttributes($attributes);

            $form->add($q->getFormFieldName(), $q->getFormType(), $attributes);
        }
        return $form;
    }

    /**
     * Send the commitment comfirmation mayMail
     * @param  Commitment $commitment
     */
    private function sendMail($commitment){
        $user = $commitment->getUser();
        $event = $commitment->getEvent();
        $dep = $commitment->getDepartment();

        $message = \Swift_Message::newInstance();
        $message->setSubject('Clanx Hölfer Bestätigung')
            ->setFrom(array('no-reply@clanx.ch'=>'Clanx Hölfer DB'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    // app/Resources/views/emails/commitmentConfirmation.html.twig
                    'emails/commitmentConfirmation.html.twig',
                    array(
                        'Forename' => $user->getForename(),
                        'Gender' => $user->getGender(),
                        'Event' => $event->getName(),
                        'EventID' => $event->getId(),
                        'EventDate' => $event->getDate(),
                        'Department' => $dep->getName(),
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    // app/Resources/views/emails/commitmentConfirmation.txt.twig
                    'emails/commitmentConfirmation.txt.twig',
                    array('Forename' => $user->getForename(),
                        'Gender' => $user->getGender(),
                        'Event' => $event->getName(),
                        'EventID' => $event->getId(),
                        'EventDate' => $event->getDate(),
                        'Department' => $dep->getName(),
                    )
                ),
                'text/plain'
            )
        ;
        $this->get('mailer')->send($message);

        $chiefUser = $dep->getChiefUser();
        if($chiefUser)
        {
            $messageToChief = \Swift_Message::newInstance();
            $messageToChief->setSubject('Neue Hölferanmeldung im Ressort '.$dep->getName())
                ->setFrom(array($user->getEmail()=>$user))
                ->setTo($chiefUser->getEmail())
                ->setBody(
                    $this->renderView('emails\commitmentNotificationToChief.html.twig',
                        array('chief' => $chiefUser,
                            'user' => $user,
                            'department' => $dep,
                            'commitment' => $commitment,
                        )
                    ),
                    'text/html'
                )
                ->addPart(
                    $this->renderView('emails\commitmentNotificationToChief.txt.twig',
                        array('chief' => $chiefUser,
                            'user' => $user,
                            'department' => $dep,
                            'commitment' => $commitment,
                        )
                    ),
                    'text/plain'
                );
                $this->get('mailer')->send($messageToChief);
        }
    }

}
