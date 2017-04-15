<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Bulk;
use AppBundle\Entity\BulkEntry;
use AppBundle\Entity\Mail;
use AppBundle\Entity\RedirectInfo;
use AppBundle\Entity\User;
use AppBundle\Form\BulkType;
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
        //TODO: add a service for this.
        $em = $this->getDoctrine()->getManager();

        $users = array();
        $roles = array();
        $bulk = new Bulk();
        foreach ($em->getRepository('AppBundle:User')->findAll() as $u) {
            $users[$u->getId()] = $u;
            $ctRoles = $u->getRoles();
            $roleStr = '';
            if(in_array('ROLE_ADMIN',$ctRoles)){$roleStr = $roleStr."Adm, ";}
            if(in_array('ROLE_SUPER_ADMIN',$ctRoles)){$roleStr = $roleStr."SA, ";}
            if(in_array('ROLE_OK',$ctRoles)){$roleStr = $roleStr."OK, ";}

            $roles[$u->getId()] = substr($roleStr, 0, -2);


            $entry = new BulkEntry();
            $entry->setId($u->getId());
            $bulk->addEntry($entry);
        }
        $choices = array(
            'Ist Vereinsmitglied' => 'make_member_key',
            'Ist nicht Vereinsmitglied' => 'remove_member_key',
            'Sende Email' => 'send_mail',
        );
        $form = $this->createForm(BulkType::class, $bulk, array('choices' => $choices, ));

        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'roles' => $roles,
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
        $mayPromoteCommittee = $this->mayPromoteCommittee($user);
        $mayDemoteCommittee = $this->mayDemoteCommittee($user);

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'may_promote_super_admin' => $mayPromoteSuperAdmin,
            'may_promote_admin' => $mayPromoteAdmin,
            'may_demote' => $mayDemote,
            'may_promote_committee' => $mayPromoteCommittee,
            'may_demote_committee' => $mayDemoteCommittee,
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
            // an admin may not demote a superadmin.
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

    private function mayPromoteCommittee($userToEdit)
    {
        if($this->isGranted('ROLE_ADMIN'))
        {
            if($userToEdit->hasRole('ROLE_OK')){
                return false;
            }
            return true;
        }
        return false;
    }

    private function mayDemoteCommittee($userToEdit)
    {
        if($this->isGranted('ROLE_ADMIN'))
        {
            if($userToEdit->hasRole('ROLE_OK')){
                return true;
            }
            return false;
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
        $user->addRole("ROLE_ADMIN");
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
        $user->removeRole("ROLE_ADMIN");
        $user->removeRole("ROLE_SUPER_ADMIN");
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
     * Adds a user to the organization committee.
     *
     * @Route("/{id}/promote/committee", name="user_promote_committee")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function promoteCommitteeAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->addRole("ROLE_OK");
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('user_show', array('id' => $user->getId()));
    }

    /**
     * Removes a user from the organization committee.
     *
     * @Route("/{id}/demote/committee", name="user_demote_committee")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function demoteCommitteeAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->removeRole("ROLE_OK");
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
