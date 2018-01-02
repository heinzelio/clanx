<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
use AppBundle\Service\IEventService;
use AppBundle\Service\IExportService;
use AppBundle\Service\IAuthorizationService;

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
     * @Route("/{id}", name="department_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Department $department, IEventService $eventSvc)
    {
        $deleteForm = $this->createDeleteForm($department);

        $shifts = $department->getShifts();
        $commitments = array();
        foreach ($department->getCommitments() as $commitment) {
            $c = $eventSvc->getCommitmentFormViewModelForEdit($commitment);
            array_push($commitments, $c);
        }
        $companions = $department->getCompanions();

        $mayDelete = $this->isGranted('ROLE_ADMIN');
        $mayDelete = $mayDelete && count($shifts) == 0;
        $mayDelete = $mayDelete && count($commitments) == 0;
        $mayDelete = $mayDelete && count($companions) == 0;

        $activeUser = $this->getUser();
        // catch null
        $userIsChief = $activeUser->isChiefOf($department);
        $userIsDeputy = $activeUser->isDeputyOf($department);


        return $this->render('department/show.html.twig', array(
            'department' => $department,
            'event' => $department->getEvent(),
            'may_delete' => $mayDelete,
            'delete_form' => $deleteForm->createView(),
            'commitments' => $commitments,
            'companions' => $companions,
            'user_is_chief' => $userIsChief,
            'user_is_deputy' => $userIsDeputy,
        ));
    }

    /**
     * Displays a form to edit an existing Department entity.
     *
     * @Route("/{id}/edit", name="department_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Department $department)
    {
        $deleteForm = $this->createDeleteForm($department, $department->getEvent());
        $editForm = $this->createForm('AppBundle\Form\DepartmentType', $department);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash('success', "'".$department->getName()."' gespeichert.");

            return $this->redirectToRoute('event_edit', array('id' => $department->getEvent()->getId()));
        }

        $shifts = $department->getShifts();
        $commitments = $department->getCommitments();
        $companions = $department->getCompanions();

        $mayDelete = $this->isGranted('ROLE_ADMIN');
        $mayDelete = $mayDelete && count($shifts) == 0;
        $mayDelete = $mayDelete && count($commitments) == 0;
        $mayDelete = $mayDelete && count($companions) == 0;

        return $this->render('department/edit.html.twig', array(
            'department' => $department,
            'event' => $department->getEvent(),
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'may_delete' => $mayDelete,
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
            ));
        }

        $editForm = $this->createForm('AppBundle\Form\DepartmentLightType', $department);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();

            $this->addFlash('success', "'".$department->getName()."' gespeichert.");

            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
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
     * @Route("/{id}", name="department_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Department $department)
    {
        $form = $this->createDeleteForm($department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($department);
            $em->flush();

            $this->addFlash('success', "Ressort ".(string)$department." erfolgreich gelöscht.");
        }

        return $this->redirectToRoute('event_show', array(
            'id'=>$department->getEvent()->getId()
        ));
    }

    /**
     * Creates a form to delete a Department entity.
     *
     * @param Department $department The Department entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Department $department)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('department_delete', array(
                'id' => $department->getId()
                )))
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
            ));
        }

        $mailData = new Mail();
        $mailData->setSender($user->getEmail());

        $commitments = $department->getCommitments();
        foreach ($commitments as $cmnt)
        {
            $usr=$cmnt->getUser();
            $mailData->addBcc($usr->getEmail(),$usr->getFullname());
        }
        $companions = $department->getCompanions();
        foreach ($companions as $companion ) {
            $mail = $companion->getEmail();
            if($mail)
            {
                $mailData->addBcc($companion->getEmail());
            }
        }
        $mailData->setSubject(
                $department->getEvent()->getName()
                ." - ".$department->getName()
                . " - Hölferinfo");
        $url = $this->generateUrl('department_show',
                                    array('id' => $department->getId(),
                                ),
                                    UrlGeneratorInterface::ABSOLUTE_URL
                                );
        $mailData->setText('Link: '.$url);

        $redirectInfo = new RedirectInfo();
        $redirectInfo->setRouteName('department_show');
        $redirectInfo->setArguments(array(
            'id' => $department->getId(),
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
     * @ParamConverter("volunteer", class="AppBundle:User", options={"id" = "user_id"})
     */
    public function moveVolunteerAction(Request $request, Department $department, User $volunteer)
    {
        $event = $department->getEvent();
        $operator=$this->getUser();
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $chiefUser= $department->getChiefUser();
            if ($operator->getId()!=$chiefUser->getId())
            {
                $this->addFlash('warning', "Du musst Admin oder Ressortleiter sein, um Hölfer verschieben zu können.");
                return $this->redirectToRoute('department_show',array(
                    'id'=>$department->getId(),
                ));
            }
        }

        $form = $this->createMoveVolunteerForm($department,$volunteer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $cmtRepo = $em->getRepository('AppBundle:Commitment');
            $cmt = $cmtRepo->findOneBy(array(
                'department'=>$department,
                'user'=>$volunteer
            ));
            $oldDepartment = $cmt->getDepartment();
            $newDepartmentId = $form->get('department')->getData();
            $newDepartment = $em->getRepository('AppBundle:Department')->findOneById($newDepartmentId);
            $cmt->setDepartment($newDepartment);
            $em->persist($cmt);
            $em->flush();

            $text = $form->get('message')->getData();
            $numSent = $this->sendMail($text,$newDepartment,$oldDepartment,$operator,$volunteer);

            $flashMsg = $volunteer.' wurde verschoben nach "'.$cmt->getDepartment()->getName().'" - ';
            if($numSent>0){
              $flashMsg=$flashMsg.'Nachricht gesendet';
            }else{
              $flashMsg=$flashMsg.'Nachricht nicht gesendet.';
            }
            $this->addFlash('success',$flashMsg);

            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
            ));
        }

        return $this->render('department/move_volunteer.html.twig',array(
            'department'=>$department,
            'user'=>$volunteer,
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
            ->add('message', TextareaType::class, array(
                'label' => 'Diese Nachricht an den Hölfer senden:',
            ))
            ->setMethod('POST')
            ->getForm()
        ;
    }

    private function sendMail($text, $newDepartment, $oldDepartment, $operator, $volunteer)
    {
        $event = $newDepartment->getEvent();
        $message = \Swift_Message::newInstance();
        $message->setSubject('Dein Einsatz am '.(string)$event.' - Ressortänderung!')
            ->setFrom($operator->getEmail())
            ->setTo($volunteer->getEmail())
            ->setBody(
                $this->renderView(
                    // app/Resources/views/emails/department_changed.html.twig
                    'emails/department_changed.html.twig',
                    array(
                        'text' => $text,
                        'newDepartment' => $newDepartment,
                        'oldDepartment' => $oldDepartment,
                        'event' => $newDepartment->getEvent(),
                        'operator' => $operator,
                        'volunteer' => $volunteer,
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    // app/Resources/views/emails/department_changed.txt.twig
                    'emails/department_changed.txt.twig',
                    array(
                        'text' => $text,
                        'newDepartment' => $newDepartment,
                        'oldDepartment' => $oldDepartment,
                        'event' => $newDepartment->getEvent(),
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
    public function printAllAction(
        Request $request,
        Department $department,
        IAuthorizationService $auth
    )
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.
        if(!$auth->maySeeCommitments($department)){
            $this->addFlash('warning', 'flash.authorization_denied');
            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
            ));
        }
        $commitments = $department->getCommitments();
        if(count($commitments)==0){
            $this->addFlash('warning', 'flash.no_data');
            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
            ));
        }

        $columns1 = array("Hölfer\nEmail\nTelefon",
                         "Stammhölfer\nBeruf\nGeb.Datum"
                    );
        $questionOrder = array();
        foreach ($department->getEvent()->getQuestions() as $question) {
            array_push($columns1,$question->getText());
            array_push($questionOrder, $question->getId());
        }
        $commitments = $department->getCommitments();
        $rows1 = array();
        foreach ($commitments as $cmt) {
            $user = $cmt->getUser();
            $regStr = $user->getIsRegular() ? 'Ja' : 'Nein';
            $usrTxt = (string) $user
                        ."\n".$user->getEmail();
            if($user->getPhone()){
                $usrTxt = $usrTxt."\n".$user->getPhone();
            }

            $dob = $user->getDateOfBirth()?$user->getDateOfBirth()->format('d.m.Y'):"";

            $usrText2 = $regStr.
                "\n".$user->getOccupation().
                "\n".$dob;
            $row = array($usrTxt,
                        $usrText2
                    );
            $answers = array();
            foreach ($cmt->getAnswers() as $answer) {
                $answers[$answer->getQuestion()->getId()] = $answer->getAnswer();
            }
            foreach ($questionOrder as $questionId) {
                array_push($row,$answers[$questionId]);
            }
            array_push($rows1,$row);
        }

        $columns2 = array("Hölfer\nEmail\nTelefon",
                        'Stammhölfer',
                         'Bemerkung',
                    );
        $companions = $department->getCompanions();
        $rows2 = array();
        foreach ($companions as $companion) {
            $regStr = $companion->getIsRegular() ? 'Ja' : 'Nein';
            $row = array((string) $companion."\n".$companion->getEmail()."\n".$companion->getPhone(),
                        $regStr,
                        $companion->getRemark(),
                    );
            array_push($rows2,$row);
        }
        if($companions && count($companions))
        {
            return $this->render('print_2_tables.html.twig',array(
                'title'=>$department->getName().' Hölferliste',
                'heading_1' => 'Eingeschriebene Hölfer',
                'columns_1'=>$columns1,
                'rows_1'=>$rows1,
                'heading_2' => 'Nicht registrierte Hölfer',
                'columns_2'=>$columns2,
                'rows_2'=>$rows2,
            ));
        }else {
            return $this->render('print_table.html.twig',array(
                'title'=>$department->getName().' Hölferliste',
                'columns'=>$columns1,
                'rows'=>$rows1,
            ));
        }
    }

    /**
     * Provides a list of all volunteers in csv format.
     *
     * @Route("/{id}/download", name="department_download")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function downloadAction(
        Request $request,
        Department $department,
        IAuthorizationService $auth,
        IExportService $exportService
    )
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.
        if(!$auth->maySeeCommitments($department)){
            $this->addFlash('warning', 'flash.authorization_denied');
            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
            ));
        }
        $commitments = $department->getCommitments();
        if(count($commitments)==0){
            $this->addFlash('warning', 'flash.no_data');
            return $this->redirectToRoute('department_show',array(
                'id'=>$department->getId(),
            ));
        }

        $questionService = $this->get('app.question');

        $questions = $questionService->getQuestionsSorted($department->getEvent());

        $rows = array();
        $head = array(
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
            'Stammhölfer');
        $event = $department->getEvent();

        foreach ($questions as $qvm) {
            array_push($head,$qvm->getText());
        }
        array_push($rows,$head);

        foreach ($department->getCommitments() as $commitment) {
            $user=$commitment->getUser();
            $row = array(
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
                $user->getIsRegular()==1?"Ja":"Nein"
            );
            foreach ($questionService->getQuestionsAndAnswersSorted($commitment) as $q) {
                array_push($row,$q->getAnswer());
            }
            array_push($rows,$row);
        }
        array_push($rows,array());
        $head2 = array(
            'Name',
            'Email',
            'Telefon',
            'Stammhölfer',
            'Bemerkung',
        );
        array_push($rows,$head2);
        foreach ($department->getCompanions() as $companion) {
            $row = array(
                $companion->getName() ,
                $companion->getEmail() ,
                $companion->getPhone() ,
                $companion->getIsRegular()==1?"Ja":"Nein" ,
                $commitment->getRemark() ,
            );
            array_push($rows,$row);
        }

        $response = $this->render('export_raw.twig',array(
            'content' => $exportService->getCsvText($rows)
        ));
        $response->headers->set('Content-Type', 'text/csv');
        $fileName = 'Helfer_'.$department->getName().'_'.$department->getEvent()->getName().'.csv';
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        return $response;
    }
}
