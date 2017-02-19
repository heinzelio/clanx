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
use AppBundle\ViewModel\Event\QuestionListViewModel;
use AppBundle\ViewModel\Event\QuestionViewModel;

/**
 * Partial event question controller.
 *
 * @Route("/event")
 */
class EventQuestionController extends Controller
{
    /**
     * Shows a list of all Departments of the given event
     *
     * @Route("/{id}/questions", name="event_questions_list")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function questionsListAction(Event $event)
    {
        $trans = $this->get('translator');
        $trans->setLocale('de'); // TODO: use real localization here.

        $auth = $this->get('app.auth');
        if (!$auth->mayShowQuestionsOfEvent($event)) {
            $this->addFlash('danger','flash.mayNotShowQuestions');
            return new Response();
        }

        $qLVM = new QuestionListViewModel();
        foreach ($event->getQuestions() as $question) {
            $qVM = new QuestionViewModel();
            $qVM->setQuestion($question)->setMayEdit($auth->mayEditQuestion($question));
            $qLVM->addQuestion($qVM);
        }

        $qLVM->setEvent($event)->setMayCreate($auth->mayAddQuestion($event));
        return $this->render('event/list_questions.html.twig',array('model'=>$qLVM));
    }

    /**
     * Handle the event, when the "create" button is clicked.
     *
     * @Route("/{id}/questions/add", name="question_new")
     * @Method({"GET"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newQuestionAction(Event $event)
    {

    }
}
