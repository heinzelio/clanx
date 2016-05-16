<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;
use AppBundle\Entity\Mail;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction(User $user)
    {
        return $this->render('user/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, User $user)
    {
        $editForm = $this->createForm('AppBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        $mayDemote = $this->mayDemote($user);
        $mayPromoteAdmin = $this->mayPromoteToAdmin($user);
        $mayPromoteSuperAdmin = $this->mayPromoteToSuperAdmin($user);

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'may_promote_super_admin'=>$mayPromoteSuperAdmin,
            'may_promote_admin'=>$mayPromoteAdmin,
            'may_demote'=>$mayDemote,
        ));
    }

    private function mayDemote(User $userToEdit)
    {
        if($this->getUser()->getID()==$userToEdit->getID())
        {
            // Nobody may demot himself. 'Cause this would lead to extinction.
            return false;
        }
        if($this->isGranted('ROLE_SUPER_ADMIN'))
        {
            // a superadmin may demote an admin or another superadmin.
            if($userToEdit->hasRole('ROLE_SUPER_ADMIN')
                || $userToEdit->hasRole('ROLE_ADMIN')
            )
            {
                return true;
            }
            // there is no need to demote a regular user.
            return false;
        }
        else if($this->isGranted('ROLE_ADMIN'))
        {
            // an admin may not demote a superadmin.d
            if($userToEdit->hasRole('ROLE_SUPER_ADMIN'))
            {
                return false;
            }
            // but he may demot another admin.
            else if($userToEdit->hasRole('ROLE_ADMIN'))
            {
                return true;
            }
            // again, there is no need to demote a regular user.
            return false;
        }
        return false;
    }

    private function mayPromoteToAdmin(User $userToEdit)
    {
        if($this->isGranted('ROLE_SUPER_ADMIN')||$this->isGranted('ROLE_ADMIN'))
        {
            // you may only promote user that are not admins yet
            if($userToEdit->hasRole('ROLE_ADMIN')
                || $userToEdit->hasRole('ROLE_SUPER_ADMIN'))
            {
                return false;
            }
            return true;
        }
        return false;
    }

    private function mayPromoteToSuperAdmin(User $userToEdit)
    {
        // only superadmins may promote other superadmins
        if($this->isGranted('ROLE_SUPER_ADMIN'))
        {
            // there is no need to promote a user that already is superadmin
            if($userToEdit->hasRole('ROLE_SUPER_ADMIN')){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Gives a user an admin role.
     *
     * @Route("/{id}/promote/admin", name="user_promote_admin")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function promoteAdminAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setRoles(array("ROLE_ADMIN")    );
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('user_show', array('id' => $user->getId()));
    }

    /**
     * removes admin roles from user
     *
     * @Route("/{id}/demote", name="user_demote")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function demoteAdminAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setRoles(array());
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('user_show', array('id' => $user->getId()));
    }

    /**
     * Gives a user an admin role.
     *
     * @Route("/{id}/promote/superadmin", name="user_promote_superadmin")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function promoteSuperAdminAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setRoles(array("ROLE_SUPER_ADMIN")    );
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('user_show', array('id' => $user->getId()));
    }

    /**
     * Send a mail to all users.
     *
     * @Route("/mail/all", name="user_mail_all")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function mailAllAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->findAll();

        $mailData = new Mail();
        foreach ($users as $u) {
            $mailData->addBcc(
                $u->getEmail(),
                $u->getForename().' '.$u->getSurname()
            );
        }
        $redirectInfo = new redirectInfo();
        $redirectInfo->setRouteName('user_index');
        $redirectInfo->setArguments(array());
        $session->set(Mail::SESSION_KEY, $mailData);
        $session->set(RedirectInfo::SESSION_KEY, $redirectInfo);

        return $this->redirectToRoute('mail_edit');
    }
}
