<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Dashboard controller.
 * @Route("/dashboard")
 */
class DashboardController extends Controller
{
    /**
    * @Route("/", name="dashboard_index")
    * @Method("GET")
    * @Security("has_role('ROLE_USER')")
    */
    public function indexAction(Request $request)
    {
        return $this->render('dashboard/index.html.twig');
    }
}

?>
