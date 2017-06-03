<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
use AppBundle\Entity\User;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Entity\Mail;
use AppBundle\ViewModel\Commitment\CommitmentViewModel;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\Form\Commitment\CommitmentType;
use AppBundle\Form\Commitment\TextQuestionViewModel;
use AppBundle\Form\EventCreateType;
use AppBundle\Form\ShirtSizeType;
use AppBundle\Service\Authorization;
use AppBundle\Service\EventService;

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
        //AppBundle\Service\EventService
        $eventSvc = $this->get('app.event');

        // should be arrays of Event objects,
        // ordered by date
        $upcoming = $eventSvc->getUpcoming();
        $passed = $eventSvc->getPassed();


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
        $form = $this->createForm('AppBundle\Form\EventCreateType', $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $defaultDpt = new Department();
            $defaultDpt->setName("Ich helfe wo ich kann");
            $defaultDpt->setEvent($event);
            $defaultDpt->setChiefUser($this->getUser());
            $em->persist($defaultDpt);

            $dptInput = $form->get('departments')->getData();
            if($dptInput)
            {
                $dptNames = explode("\n", $dptInput);

                foreach ($dptNames as $dptName) {
                    $dpt = new Department();
                    $dpt->setName(trim($dptName));
                    $dpt->setEvent($event);
                    $em->persist($dpt);
                }
            }

            $em->flush();

            $this->addFlash('success', "'".$event->getName()."' gespeichert.");
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
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    // Place this method at the end. Because the route is just /event/id.
    // It must come after routes like /event/new or event/whaterever.
    // Otherwise when the client calls /event/new, symfony tries to open
    // an event with the id "new" (what a silly little framework...)
    public function showAction(Request $request, Event $event)
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        //AppBundle\Service\EventService
        $eventSvc = $this->get('app.event');
        $auth = $this->get('app.auth');

        $authResult = $auth->mayShowEventDetail($event);
        if(!$authResult[Authorization::VALUE]){
            $this->addFlash('danger', $authResult[Authorization::MESSAGE]);
            return $this->redirectToRoute('event_index');
        }

        if($auth->mayEnroll($event)){
            $formVM = $eventSvc->getCommitmentFormViewModel($event); //CommitmentViewModel
            $enrollForm = $this->createEnrollForm($formVM); //Symfony\Component\Form\Form
            $this->handleEnrollForm($request, $enrollForm, $formVM, $event);
        }

        //EventShowViewModel
        $detailViewModel = $eventSvc->getDetailViewModel($event);
        $detailViewModel->setDeleteForm($this->createDeleteForm($event)->createView());
        if (isset($enrollForm)) {
            $detailViewModel->setEnrollForm($enrollForm->createView());
        }

        return $this->render('event/show.html.twig', array('view_model'=>$detailViewModel));
    }
    /**
     *
     * @param  CommitmentViewModel $vm
     * @return Symfony\Component\Form\Form
     */
    public function createEnrollForm(CommitmentViewModel $vm)
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

    public function handleEnrollForm(Request $request, Form $enrollForm, CommitmentViewModel $formVM, Event $event)
    {
        $enrollForm->handleRequest($request);
        if(!$enrollForm->isSubmitted())
        {
            return;
        }
        if ($enrollForm->isValid()) {
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
        } else {
            $this->addFlash('danger','flash.enroll_required_data_missing');
        }
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
        $em = $this->getDoctrine()->getManager();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', "Änderung gespeichert.");

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
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
            $dpts = $em->getRepository('AppBundle:Department')->findByEvent($event);
            foreach ($dpts as $dpt) {
                $em->remove($dpt);
            }
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
     * Send the commitment comfirmation mayMail
     * @param  Commitment $commitment
     */
    private function sendMail($commitment)
    {
        if (!$commitment) {
            return;
        }

        $mailBuilder = $this->get('app.mail_builder');
        $mailer = $this->get('mailer');
        $message = $mailBuilder->buildCommitmentConfirmation($commitment);
        $mailer->send($message);

        if ($commitment->getDepartment() && $commitment->getDepartment()->getChiefUser()) {
            $messageToChief = $mailBuilder->buildNotificationToChief($commitment);
            $mailer->send($messageToChief);
        }
    }

    /**
     * send an invitation mail to all users who may enroll on the given event
     * @param  Request $request The request.
     * @param  Event   $event   The event.
     * @return view           The view.
     *
     * @Route("/{id}/invite", name="event_invite")
     * @Method({"GET","POST"})
     */
    public function invite(Request $request, Event $event)
    {
        $session = $request->getSession();
        $auth = $this->get('app.auth');
        $mayInvite = $auth->maySendInvitation($event);

        if (!$mayInvite) {
            $this->addFlash('warning', 'Du darfst keine Einladungen versenden.');
            return $this->redirectToRoute('event_show',array('id'=>$event->getId(),));
        }

        $mailData = new Mail();
        $eventUrl = $this->generateUrl('event_show',
                                 array('id' => $event->getId()),
                                 UrlGeneratorInterface::ABSOLUTE_URL
                             );

        $mailData->setSubject($event->getName() . " - Einladung zum mitmachen!")
            ->setSender($this->getUser()->getEmail())
            ->setText('Link: '.$eventUrl)
            ;

        $userService = $this->get('app.user');
        if ($event->getIsForAssociationMembers()) {
            $users = $userService->getAllAssociationMembers();
        } else {
            $users = $userService->getAllUsers();
        }

        foreach ($users as $usr) {
            $mail = $usr->getEmail();
            $name = $usr->getForename().' '.$usr->getSurname();
            $mailData->addBcc($mail, $name);
        }

        $session->set(Mail::SESSION_KEY, $mailData);

        $backLink = new RedirectInfo();
        $backLink->setRouteName('event_show')
            ->setArguments(array('id'=>$event->getId()))
            ;

        $session->set(RedirectInfo::SESSION_KEY, $backLink);

        return $this->redirectToRoute('mail_edit');

    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/{id}/mail/enrolled", name="event_mail_enrolled")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function mailEnrolledAction(Request $request, Event $event)
    {
        $session = $request->getSession();

        $mailData = new Mail();
        $eventUrl = $this->generateUrl('event_show',
                                 array('id' => $event->getId()),
                                 UrlGeneratorInterface::ABSOLUTE_URL
                             );

        $mailData->setSubject($event->getName() . " Hölferinfo")
             ->setSender($this->getUser()->getEmail())
             ->setText('Link: '.$eventUrl)
             ;

        foreach ($event->getCommitments() as $cmnt) {
            $usr = $cmnt->getUser();
            $mail = $usr->getEmail();
            $name = $usr->getForename().' '.$usr->getSurname();
            $mailData->addBcc($mail, $name);
        }
        foreach ($event->getCompanions() as $companion ) {
            $mail = $companion->getEmail();
            if($mail)
            {
                $mailData->addBcc($mail);
            }
        }

        $session->set(Mail::SESSION_KEY, $mailData);

        $backLink = new RedirectInfo();
        $backLink->setRouteName('event_show')
                 ->setArguments(array('id'=>$event->getId()))
                 ;

        $session->set(RedirectInfo::SESSION_KEY, $backLink);

        return $this->redirectToRoute('mail_edit');
    }

    /**
     * Prepares session variables and redirects to the MailController
     *
     * @Route("/{id}/redirect/mail/to/{user_id}", name="event_redirect_mail_to")
     * @Method("GET")
     * @ParamConverter("recipient", class="AppBundle:User", options={"id" = "user_id"})
     * @Security("has_role('ROLE_USER')")
     */
    public function redirectMailToAction(Request $request, Event $event, User $recipient)
    {
        // just in case somone clicks on the "mailTo" button of the
        // chief-of-department or deputy-of-department:
        $session = $request->getSession();

        $backLink = new RedirectInfo();
        $backLink->setRouteName('event_show')
                 ->setArguments(array('id'=>$event->getId()));
        $session->set(RedirectInfo::SESSION_KEY,$backLink);

        $mailData = new Mail();
        $mailData->setSubject('Frage betreffend '.$event->getName())
             ->setRecipient($recipient->getEmail())
             ->setSender($this->getUser()->getEmail());

        $session->set(Mail::SESSION_KEY, $mailData);

        return $this->redirectToRoute('mail_edit');
    }

    /**
     * Download all volunteers and companions as csv.
     *
     * @Route("/{id}/download", name="event_download")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function downloadAction(Request $request, Event $event)
    {
        if(! $this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_OK'))
        {
            $this->addFlash('warning', "Du musst Admin oder OK Mitglied sein, um Hölferdaten herunterladen zu können.");
            return $this->redirectToRoute('event_show',array(
                'id'=>$event->getId(),
            ));
        }

        $head = $this->array2csv(array(
            'Ressort',
            'Vorname',
            'Nachname',
            'Geschlecht',
            'Geb.Datum',
            'Strasse',
            'PLZ',
            'Ort',
            'Email',
            'Telefon',
            'Beruf',
            'Stammhölfer',
            'T-Shirt',
            'Zugbillet',
            'Mögliche Arbeitstage',
            'Bemerkung',
        ));
        $rows = array();

        foreach ($event->getDepartments() as $department ) {

            foreach ($department->getCommitments() as $commitment) {
                $user=$commitment->getUser();
                $row = $this->array2csv(array(
                    (string)$department,
                    $user->getForename() ,
                    $user->getSurname() ,
                    $user->getGender() ,
                    $user->getDateOfBirth()?$user->getDateOfBirth()->format('d.m.Y'):"" ,
                    $user->getStreet() ,
                    $user->getZip() ,
                    $user->getCity() ,
                    $user->getEmail() ,
                    $user->getPhone() ,
                    $user->getOccupation() ,
                    $user->getIsRegular()==1?"Ja":"Nein" ,
                    $commitment->getShirtSize() ,
                    $commitment->getNeedTrainTicket()==1?"Ja":"Nein" ,
                    $commitment->getPossibleStart() ,
                    $commitment->getRemark() ,
                ));
                array_push($rows,$row);
            }
            foreach ($department->getCompanions() as $companion) {
                $row = $this->array2csv(array(
                    (string)$department,
                    $companion->getName() ,
                    '' ,
                    '' ,
                    '' ,
                    '' ,
                    '' ,
                    '' ,
                    $user->getEmail() ,
                    $user->getPhone() ,
                    '' ,
                    $user->getIsRegular()==1?"Ja":"Nein" ,
                    '' ,
                    '' ,
                    '' ,
                    $commitment->getRemark() ,
                ));
                array_push($rows,$row);
            }
        }

        $response = $this->render('export.csv.twig',array(
            'head' => $head,
            'rows' => $rows
        ));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        return $response;

    }


    private function array2csv($arrayRow)
    {
        // https://stackoverflow.com/questions/4249432/export-to-csv-via-php
        //$delimiter = chr(9); // tab (for excel)
        $delimiter = ';';
        $enclosure = '"';
        $escape = '\\';

        ob_start();
        $df = fopen("php://output", 'w');
        //add BOM to fix UTF-8 in Excel
        fputs($df, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        fputcsv($df, $arrayRow, $delimiter,$enclosure);
        fclose($df);
        return ob_get_clean();
    }


}
