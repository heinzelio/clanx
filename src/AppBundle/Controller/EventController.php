<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
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
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Request $request, Event $event)
    {
        //AppBundle\Service\EventService
        $eventSvc = $this->get('app.event');
        $auth = $this->get('app.auth');

        $authResult = $auth->mayShowEventDetail($event);
        if(!$authResult[Authorization::VALUE]){
            $this->get('session')->getFlashBag()->add('danger', $authResult[Authorization::MESSAGE]);
            return $this->redirectToRoute('event_index');
        }

        $detailViewModel = $eventSvc->getDetailViewModel($event);

        $detailViewModel['delete_form'] = $this->createDeleteForm($event)
                                                ->createView();
        $detailViewModel['enroll_form'] = $this->createEnrollForm($event,$event->getFreeDepartments())
                                                ->createView();

        return $this->render('event/show.html.twig', $detailViewModel);
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

            $this->get('session')->getFlashBag()->add('success', "'".$event->getName()."' gespeichert.");
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/new.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
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
        $em = $this->getDoctrine()->getManager();
        $departments = $em->getRepository('AppBundle:Department')->findByEvent($event);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($event);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Änderung gespeichert.");

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/edit.html.twig', array(
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'departments' => $departments
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
     *
     */
    private function createEnrollForm(Event $event, $departments)
    {
        $threeDays = new \DateInterval('P2D');
        $laterDate = clone $event->getDate();
        $laterDate->add($threeDays);
        $emptyData = $event->getDate()->format('D, d.m.Y')
        ." 08:00 - "
        .$laterDate->format('D, d.m.Y')
        . " 20:00";
        return $this->createFormBuilder()
            ->add('department', EntityType::class, array(
                'class'=>'AppBundle:Department',
                'label' => 'Für Ressort (ohne Garantie)',
                'choices' => $departments,
                'choice_label' => function ($dpt) {
                                        return $dpt->getLongText();
                                    }
            ))
            ->add('possibleStart', TextareaType::class, array(
                'label' => 'Ich helfe an folgenden Tagen (bitte auch Zeit angeben)',
                'data' => $emptyData,
                'attr' => array(
                    'rows' => 4
                )
            ))
            ->add('shirtSize',ShirtSizeType::class,array(
                'label' => 'TShirt Grösse',
            ))
            ->add('needTrainTicket', CheckboxType::class, array(
                'label' => 'Ich brauche ein Zugbillet',
                'attr' => array('checked'=>false),
                'required' => false,
            ))
            ->add('remark', TextareaType::class, array(
                'label' => "Bemerkung / Wunsch",
                'required' => false,
                'attr' => array(
                    'rows' => 4
                )
            ))
            ->setAction($this->generateUrl('event_enroll', array('id' => $event->getID())))
            ->setMethod('POST')
            ->getForm()
        ;
    }

    /**
     * Enrolls user as volunteer.
     *
     * @Route("/enroll/{id}", name="event_enroll")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function enrollAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $depRep = $em->getRepository('AppBundle:Department');
        $form = $this->createEnrollForm($event,$event->getDepartments());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depId = $form->get('department')->getData();
            $startDate = $form->get('possibleStart')->getData();
            $possStartFormItem = $form->get('possibleStart');
            $shirtSize = $form->get('shirtSize')->getData();
            $needTrainTicket = $form->get('needTrainTicket')->getData();
            $remark = $form->get('remark')->getData();
            $dep = $depRep->findOneById($depId);
            $user = $this->getUser();
            $c = new Commitment();
            $c->setUser($user);
            $c->setEvent($event);
            $c->setDepartment($dep);
            $c->setPossibleStart($startDate);
            $c->setShirtSize($shirtSize);
            $c->setNeedTrainTicket($needTrainTicket);
            $c->setRemark($remark);
            $session = $request->getSession();
            if($dep->commitmentExists($c))
            {
                $session->getFlashBag()->add('warning', 'Du bist bereits in diesem Ressort als Hölfer eingetragen.');
            }
            else {
                try{
                    $em->persist($c);
                    $em->flush();
                    $this->sendMail($user, $dep, $event, $c);
                    $session->getFlashBag()->add('success', 'Du bist als Hölfer eingetragen. Checke deine Emails für mehr Infos (Wenn du kein Email findest, schau bitte auch im Spamordner nach).');
                }catch(Exception $ex){
                    $session->getFlashBag()->add('error', 'Speichern fehlgeschlagen. Du bist NICHT eingetragen. Versuche es später nochmal.');
                }
            }
        }

        return $this->redirectToRoute('event_show', array('id' => $event->getID()));
    }

    private function sendMail($user,$dep,$event,$commitment){
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
    public function downloadActiom(Request $request, Event $event)
    {
        if(! $this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_OK'))
        {
            $this->get('session')->getFlashBag()
                ->add('warning', "Du musst Admin oder OK Mitglied sein, um Hölferdaten herunterladen zu können.");
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
