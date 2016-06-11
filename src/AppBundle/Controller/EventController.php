<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
     * Finds and displays a Event entity.
     *
     * @Route("/{id}", name="event_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $depRep = $em->getRepository('AppBundle:Department');
        $departments = $depRep->findByEvent($event);
        // find out of which departments i am a chief.
        $myDepartmentsAsChief = $depRep->findBy(
            array('chiefUser' => $user, 'event' => $event)
        );
        $myDepartmentsAsDeputy = $depRep->findBy(
            array('deputyUser' => $user, 'event' => $event)
        );

        $enrollForm = $this->createEnrollForm($event,$departments);
        $deleteForm = $this->createDeleteForm($event);

        // todo: find the values for these:
        // maybe consider the use of "voters"
        // https://symfony.com/doc/current/cookbook/security/voters.html#how-to-use-the-voter-in-a-controller

        $commitments = $em->getRepository('AppBundle:Commitment');

        $enrolledCount = $commitments->countFor($event);

        $commitment = $commitments->findOneBy(array(
            'user' => $user,
            'event' => $event,
        ));

        $mayEnroll = !$commitment && $event->enrollmentPossible();

        // if event is further than 2 weeks from now, a user my change his commitment.
        $twoWeeksFromNow = (new \DateTime())->add(new \DateInterval('P2W'));
        $mayEditCommitment = ($twoWeeksFromNow < $event->getDate());

        $mayMail = $this->isGranted('ROLE_ADMIN');

        $mayEdit = $this->isGranted('ROLE_ADMIN') && $event->mayEdit();
        $mayDelete = $this->isGranted('ROLE_SUPER_ADMIN') && $event->mayDelete();

        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'enroll_form' => $enrollForm->createView(),
            'mayEnroll' => $mayEnroll,
            'enrolledCount' => $enrolledCount,
            'commitment' => $commitment,
            'mayEditCommitment' => $mayEditCommitment,
            'mayMail' => $mayMail,
            'mayEdit' => $mayEdit,
            'mayDelete' => $mayDelete,
            'myDepartmentsAsChief' => $myDepartmentsAsChief,
            'myDepartmentsAsDeputy' => $myDepartmentsAsDeputy,
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
        $choices = array();
        foreach ($departments as $dep) {
            if($dep->getRequirement()){
                $text = $dep->getName().' ('.$dep->getRequirement().')';
            }else{
                $text = $dep->getName();
            }
            $choices[$text] = $dep->getID();
        }
        $emptyData = $event->getDate()->format('d.m.Y')." 08:00";
        return $this->createFormBuilder()
            ->add('department', ChoiceType::class, array(
                'label' => 'für Ressort (ohne Garantie)',
                'choices'  => $choices
            ))
            ->add('possibleStart', TextType::class, array(
                'label' => 'frühestes Startdatum & Zeit',
                'data' => $emptyData,
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
                'required' => false
            ))
            ->setAction($this->generateUrl('event_enroll', array('id' => $event->getID())))
            ->setMethod('POST')
            ->getForm()
        ;
    }

    /**
     * Finds and displays a Event entity.
     *
     * @Route("/enroll/{id}", name="event_enroll")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function enrollAction(Request $request, Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $depRep = $em->getRepository('AppBundle:Department');
        $departments = $depRep->findByEvent($event);
        $form = $this->createEnrollForm($event,$departments);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depId = $form->get('department')->getData();
            $startDate = $form->get('possibleStart')->getData();
            $possStartFormItem = $form->get('possibleStart');
            $shirtSize = $form->get('shirtSize')->getData();
            $needTrainTicket = $form->get('needTrainTicket')->getData();
            $dep = $depRep->findOneById($depId);
            $user = $this->getUser();
            $c = new Commitment();
            $c->setUser($user);
            $c->setEvent($event);
            $c->setDepartment($dep);
            $c->setPossibleStart($startDate);
            $c->setShirtSize($shirtSize);
            $c->setNeedTrainTicket($needTrainTicket);
            $c->setRemark($form->get('remark')->getData());
            $session = $request->getSession();
            try{
                $em->persist($c);
                $em->flush();
                $this->sendMail($user, $dep, $event, $c);
            }catch(Exception $ex){
                $session->getFlashBag()->add('error', 'Speichern fehlgeschlagen. Du bist NICHT eingetragen. Versuche es später nochmal.');
            }
        }

        return $this->redirectToRoute('event_show', array('id' => $event->getID()));
    }

    private function sendMail($user,$dep,$event,$commitment){
        $message = \Swift_Message::newInstance();
        $dankeImgLink =  $message->embed(\Swift_Image::fromPath('img/emails/danke.png'));
        $message->setSubject('Clanx Hölfer Bestätigung')
            ->setFrom(array('no-reply@clanx.ch'=>'Clanx Hölfer DB'))
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    // app/Resources/views/emails/CommitmentConfirmation.html.twig
                    'emails/CommitmentConfirmation.html.twig',
                    array('Forename' => $user->getForename(),
                        'Event' => $event->getName(),
                        'EventID' => $event->getId(),
                        'EventDate' => $event->getDate(),
                        'Department' => $dep->getName(),
                        'DankeImgLink' => $dankeImgLink,
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    // app/Resources/views/emails/CommitmentConfirmation.txt.twig
                    'emails/CommitmentConfirmation.txt.twig',
                    array('Forename' => $user->getForename(),
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
        $em = $this->getDoctrine()->getManager();

        $mailData = new Mail();
        $eventUrl = $this->generateUrl('event_show',
                                 array('id' => $event->getId()),
                                 UrlGeneratorInterface::ABSOLUTE_URL
                             );

        $mailData->setSubject($event->getName() . " Hölferinfo")
             ->setSender($this->getUser()->getEmail())
             ->setText('Link: '.$eventUrl)
             ;

        $commitmentsRep = $em->getRepository('AppBundle:Commitment');
        $commitments=$commitmentsRep->findByEvent($event);
        foreach ($commitments as $cmnt) {
            $usr = $cmnt->getUser();
            $eml = $usr->getEmail();
            $nme = $usr->getForename().' '.$usr->getSurname();
            $mailData->addBcc($eml, $nme);
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
}
