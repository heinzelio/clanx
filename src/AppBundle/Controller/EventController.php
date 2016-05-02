<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Department;
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
            $em->persist($defaultDpt);

            $dptInput = $form->get('departments')->getData();
            $dptNames = explode("\n", $dptInput);

            foreach ($dptNames as $dptName) {
                $dpt = new Department();
                $dpt->setName(trim($dptName));
                $dpt->setEvent($event);
                $em->persist($dpt);
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
    public function showAction(Event $event)
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

        $isEnrolled = $commitments->existsFor( $user ,$event);

        $mayEnroll = !$isEnrolled && $event->enrollmentPossible();

        $mayMail = $this->isGranted('ROLE_ADMIN');

        $mayEdit = $this->isGranted('ROLE_ADMIN') && $event->mayEdit();
        $mayDelete = $this->isGranted('ROLE_SUPER_ADMIN') && $event->mayDelete();

        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'enroll_form' => $enrollForm->createView(),
            'mayEnroll' => $mayEnroll,
            'enrolledCount' => $enrolledCount,
            'isEnrolled' => $isEnrolled,
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

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
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
        return $this->createFormBuilder()
            ->add('department', ChoiceType::class, array(
                'label' => 'für Ressort (ohne Garantie)',
                'choices'  => $choices
            ))
            ->add('possibleStart', DateTimeType::class,array(
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                //'html5' => true,
                //'input' => 'array',
                'label' => 'früheste Startzeit',
                'data' => $event->getDate()
            ))
            ->add('shirtSize',ShirtSizeType::class,array(
                'label' => 'TShirt Grösse',
            ))
            ->add('remark', TextareaType::class, array(
                'label' => "Bemerkung / Wunsch",
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Eintragen',
                'attr' => array('class'   => 'btn btn-danger',
                                'type'    => 'submit',
                                'onclick' => 'return confirm("Willst du dich wirklich bei '.$event->getName().' eintragen?")'
                                )
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
            $dep = $depRep->findOneById($depId);
            $user = $this->getUser();
            $c = new Commitment();
            $c->setUser($user);
            $c->setEvent($event);
            $c->setDepartment($dep);
            $c->setPossibleStart($startDate);
            $c->setShirtSize($shirtSize);
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

    private function sendMail($user,$dep,$event){
        $message = \Swift_Message::newInstance();
        $dankeImgLink =  $message->embed(\Swift_Image::fromPath('img/emails/danke.png'));
        $message->setSubject('Clanx Hölfer Bestätigung')
            ->setFrom('noreply@clanx.com')

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
        $mailForm = $this->createForm('AppBundle\Form\EventMailType', $event);
        $mailForm->handleRequest($request);

        if ($mailForm->isSubmitted() && $mailForm->isValid()) {

            $text = $mailForm->get('text')->getData();
            $subject = $mailForm->get('subject')->getData();
            $message = \Swift_Message::newInstance();
            $message->setSubject($subject)
                ->setFrom('noreply@clanx.com')
                ->addPart(
                    $text,
                    'text/plain'
                );

            $em = $this->getDoctrine()->getManager();
            $commitmentsRep = $em->getRepository('AppBundle:Commitment');
            $commitments=$commitmentsRep->findByEvent($event);
            $doSend=false;
            foreach ($commitments as $cmnt) {
                $doSend=true;
                $usr=$cmnt->getUser();
                $message->setBcc($usr->getEmail());
            }

            if($doSend){
                $mailer = $this->get('mailer');
                $numSent = $mailer->send($message);
                $flashbag = $request->getSession()->getFlashBag();
                $flashbag->add('success', $numSent.' EMails verschickt.');
            }else {
                $flashbag = $request->getSession()->getFlashBag();
                $flashbag->add('warning', 'Keine Email geschick.');
            }



            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        // only on unsubmitted forms:
        $mailForm->get('subject')->setData($event->getName() . " Hölferinfo");
        $url = $this->generateUrl('event_show',
                                    array('id' => $event->getId()),
                                    UrlGeneratorInterface::ABSOLUTE_URL
                                );
        $mailForm->get('text')->setData($url);
        return $this->render('event/mail_enrolled.html.twig', array(
            'event' => $event,
            'mail_form' => $mailForm->createView(),
        ));
    }
}
