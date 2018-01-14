<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Companion;
use App\Entity\Department;
use App\Form\CompanionType;

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
     * @Security("has_role('ROLE_USER')")
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

        $companion = new Companion();
        $form = $this->createForm('App\Form\CompanionType', $companion);
        // the action of the form is changed in the view!
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $companion->setDepartment($department);
            $em->persist($companion);
            $em->flush();
            $this->addFlash('success', "'".$companion->getName()."' gespeichert.");

            return $this->redirectToRoute('department_show', array(
                'id' => $department->getId(),
            ));
        }

        return $this->render('department/new_companion.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
        ));
    }

    /**
     * Deletes a companion.
     *
     * @Route("/{id}", name="companion_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, Companion $companion)
    {
        $user = $this->getUser();
        $department = $companion->getDepartment();

        if (! $user->isChiefOf($department)
            && ! $user->isDeputyOf($department)
            && ! $this->isGranted('ROLE_ADMIN'))
        {
            $this->addFlash('warning', "Du bist dafür nicht authorisiert.");
        }else{
            $em = $this->getDoctrine()->getManager();
            $em->remove($companion);
            $em->flush();
            $this->addFlash('success', "Hölfer ".$companion->getName()." gelöscht.");
        }

        return $this->redirectToRoute('department_show',array(
            'id' => $department->getId(),
        ));
    }
}
