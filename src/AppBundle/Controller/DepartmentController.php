<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Department;
use AppBundle\Entity\Event;
use AppBundle\Entity\User;
use AppBundle\Entity\Mail;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Form\DepartmentType;

/**
 * Department controller.
 *
 * @Route("/department")
 */
class DepartmentController extends Controller
{
    /**
     * Creates a new Department entity.
     *
     * @Route("/new/for/event/{event_id}", name="department_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function newAction(Request $request, Event $event)
    {
        $department = new Department();
        $form = $this->createForm('AppBundle\Form\DepartmentType', $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $department->setEvent($event);
            $em->persist($department);
            $em->flush();

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('department/new.html.twig', array(
            'department' => $department,
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Department entity.
     *
     * @Route("/{id}/of/event/{event_id}", name="department_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function showAction(Department $department,Event $event)
    {
        $deleteForm = $this->createDeleteForm($department,$event);
        $em = $this->getDoctrine()->getManager();
        $commRepo = $em->getRepository('AppBundle:Commitment');
        $shiftRepo = $em->getRepository('AppBundle:Shift');

        $qb = $em->createQueryBuilder();
        $qb->select('count(shift.id)')
        ->from('AppBundle:Shift','shift')
        ->where('shift.department = :dpt')
        ->setParameter('dpt', $department);
        $countShift = $qb->getQuery()->getSingleScalarResult();

        $qb = $em->createQueryBuilder();
        $qb->select('count(cmt.id)')
        ->from('AppBundle:Commitment','cmt')
        ->where('cmt.department = :dpt')
        ->setParameter('dpt', $department);
        $countCommitment = $qb->getQuery()->getSingleScalarResult();

        $userRepo = $em->getRepository('AppBundle:User');
        $commitments = $commRepo->findByDepartment($department);

        $mayDelete = $this->isGranted('ROLE_ADMIN');
        $mayDelete = $mayDelete && $countShift == 0;
        $mayDelete = $mayDelete && $countCommitment == 0;

        $activeUser = $this->getUser();
        $chiefUser = $department->getChiefUser();
        $deputyUser = $department->getDeputyUser();
        // catch null
        $userIsChief = $activeUser->isChiefOf($department);
        $userIsDeputy = $activeUser->isDeputyOf($department);


        return $this->render('department/show.html.twig', array(
            'department' => $department,
            'event' => $event,
            'mayDelete' => $mayDelete,
            'delete_form' => $deleteForm->createView(),
            'commitments' => $commitments,
            'userIsChief' => $userIsChief,
            'userIsDeputy' => $userIsDeputy,
        ));
    }

    /**
     * Displays a form to edit an existing Department entity.
     *
     * @Route("/{id}/of/event/{event_id}/edit", name="department_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function editAction(Request $request, Department $department, Event $event)
    {
        $deleteForm = $this->createDeleteForm($department,$event);
        $editForm = $this->createForm('AppBundle\Form\DepartmentType', $department);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "'".$department->getName()."' gespeichert.");

            return $this->redirectToRoute('event_edit', array('id' => $event->getId()));
        }

        return $this->render('department/edit.html.twig', array(
            'department' => $department,
            'event' => $event,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit only a few fields of an existing Department entity.
     * Used only by chiev_of_department
     *
     * @Route("/{id}/edit/light", name="department_edit_light")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function editLightAction(Request $request, Department $department)
    {
        if(! $this->getUser()->isChiefOf($department)
            &&
            ! $this->isGranted('ROLE_ADMIN')
        )
        {
            return $this->redirectToRoute('department_show',array(
                'id' => $department->getId(),
                'event_id' => $department->getEvent()->getID(),
            ));
        }

        $editForm = $this->createForm('AppBundle\Form\DepartmentLightType', $department);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "'".$department->getName()."' gespeichert.");

            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
                'event_id'=>$department->getEvent()->getId()
            ));
        }

        return $this->render('department/edit_light.html.twig', array(
            'department' => $department,
            'event' => $department->getEvent(),
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Department entity.
     *
     * @Route("/{id}/of/event/{event_id}", name="department_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("event", class="AppBundle:Event", options={"id" = "event_id"})
     */
    public function deleteAction(Request $request, Department $department, Event $event)
    {
        $form = $this->createDeleteForm($department,$event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($department);
            $em->flush();
            //TODO: Add msg to flashbag.
        }

        return $this->redirectToRoute('event_edit', array('id'=>$event->getId()));
    }

    /**
     * Creates a form to delete a Department entity.
     *
     * @param Department $department The Department entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Department $department, Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('department_delete', array(
                'id' => $department->getId(),
                'event_id'=>$event->getId())
            ))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Send an email to all volunteers of a department.
     *
     * @Route("/{id}/mail/volunteers", name="department_mail_volunteers")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function mailVolunteersAction(Request $request, Department $department)
    {
        $user = $this->getUser();
        if( !$this->isGranted('ROLE_ADMIN')
            && $user != $department->getChiefUser()
            && $user != $department->getDeputyUser())
        {
            $this->redirectToRoute('department_show',array(
                'id'=> $department->getId(),
                'event_id' => $department->getEvent()->getId()
            ));
        }

        $mailData = new Mail();
        $mailData->setSender($user->getEmail());

        $em = $this->getDoctrine()->getManager();
        $commitmentsRep = $em->getRepository('AppBundle:Commitment');
        $commitments=$commitmentsRep->findByDepartment($department);
        foreach ($commitments as $cmnt)
        {
            $usr=$cmnt->getUser();
            $mailData->addBcc($usr->getEmail(),$usr->getFullname());
        }
        $mailData->setSubject(
                $department->getEvent()->getName()
                ." - ".$department->getName()
                . " - Hölferinfo");
        $url = $this->generateUrl('department_show',
                                    array('id' => $department->getId(),
                                    'event_id' => $department->getEvent()->getId()
                                ),
                                    UrlGeneratorInterface::ABSOLUTE_URL
                                );
        $mailData->setText('Link: '.$url);

        $redirectInfo = new RedirectInfo();
        $redirectInfo->setRouteName('department_show');
        $redirectInfo->setArguments(array(
            'id' => $department->getId(),
            'event_id' => $department->getEvent()->getId()
        ));

        $session = $request->getSession();
        $session->set(Mail::SESSION_KEY,$mailData);
        $session->set(RedirectInfo::SESSION_KEY,$redirectInfo);

        return $this->redirectToRoute('mail_edit');
    }


    /**
     * Deletes a Department entity.
     *
     * @Route("/{id}/move/volunteer/{user_id}", name="department_move_volunteer")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("user", class="AppBundle:User", options={"id" = "user_id"})
     */
    public function moveVolunteerAction(Request $request, Department $department, User $user)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $chiefUser= $department->getChiefUser();
            $thisUser=$this->getUser();
            if ($thisUser->getId()!=$chiefUser->getId())
            {
                $this->get('session')->getFlashBag()
                    ->add('warning', "Du musst Admin oder Ressortleiter sein, um Hölfer verschieben zu können.");
                return $this->redirectToRoute('department_show',array(
                    'id'=>$department->getId(),
                    'event_id'=>$department->getEvent()->getId()
                ));
            }
        }

        $form = $this->createMoveVolunteerForm($department,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cmtRepo = $em->getRepository('AppBundle:Commitment');
            $cmt = $cmtRepo->findOneBy(array(
                'department'=>$department,
                'user'=>$user
            ));
            $targetDptId = $form->get('department')->getData();
            $targetDpt = $em->getRepository('AppBundle:Department')->findOneById($targetDptId);
            $cmt->setDepartment($targetDpt);
            $em->persist($cmt);
            $em->flush();
            $this->get('session')->getFlashBag()
            ->add('success',
                'Hölfer wurde verschoben nach '
                .$cmt->getDepartment()->getName());

            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
                'event_id'=>$department->getEvent()->getId()
            ));
        }

        return $this->render('department/move_volunteer.html.twig',array(
            'department'=>$department,
            'user'=>$user,
            'move_form' => $form->createView(),
        ));
    }

    private function createMoveVolunteerForm(Department $department, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $allDepartments = $em->getRepository('AppBundle:Department')->findByEvent($department->getEvent());

        $choices = array();
        foreach ($allDepartments as $dpt)
        {
            if($dpt->getId() != $department->getId())
            {
                $choices[$dpt->getName()] = $dpt->getID();
            }
        }

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('department_move_volunteer', array(
                'id' => $department->getId(),
                'user_id'=>$user->getId())
            ))
            ->add('department', ChoiceType::class, array(
                'label' => '',
                'choices'  => $choices
            ))
            ->setMethod('POST')
            ->getForm()
        ;
    }

    /**
     * Prepares session variables and redirects to the MailController
     *
     * @Route("/{id}/redirect/mail/to/{user_id}", name="department_redirect_mail_to")
     * @Method("GET")
     * @ParamConverter("recipient", class="AppBundle:User", options={"id" = "user_id"})
     * @Security("has_role('ROLE_USER')")
     */
    public function redirectMailToAction(Request $request, Department $department, User $recipient)
    {
        // just in case somone clicks on the "mailTo" button of the
        // chief-of-department or deputy-of-department:
        $session = $request->getSession();

        $backLink = new RedirectInfo();
        $backLink->setRouteName('department_show')
                 ->setArguments(array(
                     'id'=>$department->getId(),
                     'event_id'=>$department->getEvent()->getId(),
             ));
        $session->set(RedirectInfo::SESSION_KEY,$backLink);

        $mailData = new Mail();
        $mailData->setSubject('Dein Einsatz am '.$department->getEvent()->getName())
             ->setRecipient($recipient->getEmail())
             ->setSender($this->getUser()->getEmail());

        $session->set(Mail::SESSION_KEY, $mailData);

        return $this->redirectToRoute('mail_edit');
    }

    /**
     * Renders a table view that can be printed
     *
     * @Route("/{id}/print/all", name="department_print_all")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function printAllAction(Request $request, Department $department)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $chiefUser= $department->getChiefUser();
            $deputyUser = $department->getDeputyUser();
            $thisUser=$this->getUser();
            if ($thisUser->getId() != $chiefUser->getId()
                &&
                $thisUser->getId() != $deputyUser->getId()
            )
            {
                $this->get('session')->getFlashBag()
                    ->add('warning', "Du musst Admin, Ressortleiter oder Stellvertreter sein, um Hölferdate drucken zu können.");
                return $this->redirectToRoute('department_show',array(
                    'id'=>$department->getId(),
                    'event_id'=>$department->getEvent()->getId()
                ));
            }
        }


         $columns = array('Hölfer',
                         'Ich helfe an folgenden Tagen',
                         'Bemerkung',
                         'Shirt',
                         'Zugbillet'
                    );
        $commitments = $department->getCommitments();
        $rows = array();
        foreach ($commitments as $cmt) {
            $row = array((string) $cmt->getUser(),
                        $cmt->getPossibleStart(),
                        $cmt->getRemark(),
                        $cmt->getShirtSize(),
                        $cmt->getNeedTrainTicket(),
                    );
            array_push($rows,$row);
        }

        return $this->render('print_table.html.twig',array(
            'title'=>$department->getName().' Hölferliste',
            'columns'=>$columns,
            'rows'=>$rows,
        ));
    }
}
