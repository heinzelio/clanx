<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Companion;
use AppBundle\Entity\Department;
use AppBundle\Form\CompanionType;

/**
 * Companion controller.
 *
 * @Route("/companion")
 */
class CompanionController extends Controller
{
    /**
     * Creates a new Companion or shows a form to create one.
     *
     * @Route("/new/for/department/{id}", name="companion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Department $department)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
        {
            $user = $this->getUser();
            if(!($user && ($user->isChiefOf($department) ||
                $user->isDeputyOf($department))))
                {
                    // it is a sub-view. do return a void response when not authorized.
                    return new Response('');
                }
        }

// that would be nice. But unfortunately it throws an authorization exception...
//        $this->denyAccessUnlessGranted(new Expression(
//            '"ROLE_ADMIN" in roles or (
//                user and (
//                    user.isChiefOf(object) or user.isDeputyOf(object)
//            ))'
//        ), $department);

        $companion = new Companion();
        $form = $this->createForm('AppBundle\Form\CompanionType', $companion);
        // the action of the form is changed in the view!
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $companion->setDepartment($department);
            $em->persist($companion);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "'".$companion->getName()."' gespeichert.");

            return $this->redirectToRoute('department_show', array(
                'id' => $department->getId(),
                'event_id' => $department->getEvent()->getId(),
            ));
        }

        return $this->render('department/new_companion.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
        ));
    }
}
