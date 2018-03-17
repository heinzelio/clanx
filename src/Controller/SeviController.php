<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Department;
use App\Entity\Commitment;
use App\Entity\LegacyUser;

/**
 * Dashboard controller.
 * @Route("/sevi")
 */
class SeviController extends Controller
{
    /**
    * @Route("/index", name="sevi_index")
    * @Method({"GET","POST"})
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */
    public function indexAction(Request $request)
    {

        return $this->render('sevi/seviView.html.twig',array(
            'theSuperDuperVariableName' => 42,
        ));
    }
}

 ?>
