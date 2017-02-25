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
use AppBundle\Entity\Commitment;
use AppBundle\ViewModel\Commitment\YesNoQuestionViewModel;
use AppBundle\ViewModel\Commitment\CommitmentViewModel;
use AppBundle\Form\Commitment\CommitmentType;
use AppBundle\Form\Commitment\TextQuestionViewModel;

/**
 * Partial event commitment controller.
 *
 * @Route("/event")
 */
class EventDepartmentController extends Controller
{
    /**
     * Shows a list of all Departments of the given event
     *
     * @Route("/{id}/departments", name="event_departments_list")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function eventListAction(Event $event)
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        $auth = $this->get('app.auth');
        if (!$auth->mayShowDepartmentsOfEvent($event)) {
            $this->addFlash('danger','flash.mayNotShowDepartments');
            return new Response();
        }

        return $this->render('event/list_departments.html.twig',array('event'=>$event));
    }
}
