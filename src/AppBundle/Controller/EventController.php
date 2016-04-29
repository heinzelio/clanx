<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Form\EventType;
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
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $depRep = $em->getRepository('AppBundle:Department');
        $departments = $depRep->findByEvent($event);
        // find out of which departments i am a chief.
        $iAmChiefOf = $depRep->findBy(
            array('chiefUser' => $user, 'event' => $event)
        );
        $chiefText = '';
        $firstLoop = true;
        foreach ($iAmChiefOf as $dep) {
            if(!$firstLoop){
                $chiefText = $chiefText.', ';
            }
            $chiefText=$chiefText . $dep->getName();
            $firstLoop = false;
        }
        $iAmAChief = !$firstLoop;

        $iAmDeputyOf = $depRep->findBy(
            array('deputyUser' => $user, 'event' => $event)
        );
        $deputyText = '';
        $firstLoop = true;
        foreach ($iAmDeputyOf as $dep) {
            if(!$firstLoop){
                $deputyText = $deputyText.', ';
            }
            $deputyText=$deputyText.$dep->getName();
            $firstLoop = false;
        }
        $iAmADeputy = !$firstLoop;

        $enrollForm = $this->createEnrollForm($event,$departments);
        $deleteForm = $this->createDeleteForm($event);

        // todo: find the values for these:
        // maybe consider the use of "voters"
        // https://symfony.com/doc/current/cookbook/security/voters.html#how-to-use-the-voter-in-a-controller

        $commitments = $em->getRepository('AppBundle:Commitment');

        $enrolledCount = $commitments->countFor($event);

        $isEnrolled = $commitments->existsFor( $this->getUser() ,$event);

        $mayEnroll = !$isEnrolled && $event->enrollmentPossible();


        $mayEdit = $this->isGranted('ROLE_ADMIN') && $event->mayEdit();
        $mayDelete = $this->isGranted('ROLE_SUPER_ADMIN') && $event->mayDelete();

        return $this->render('event/show.html.twig', array(
            'event' => $event,
            'delete_form' => $deleteForm->createView(),
            'enroll_form' => $enrollForm->createView(),
            'mayEnroll' => $mayEnroll,
            'enrolledCount' => $enrolledCount,
            'isEnrolled' => $isEnrolled,
            'mayEdit' => $mayEdit,
            'mayDelete' => $mayDelete,
            'isChief' => $iAmAChief,
            'isDeputy' => $iAmADeputy,
            'chiefOfDepartment' => $chiefText,
            'deputyOfDepartment' => $deputyText
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
}
