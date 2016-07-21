<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Department;
use AppBundle\Entity\Event;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\Companion;

/**
 * Department API controller.
 *
 * @Route("/api/departments")
 */
class DepartmentController extends Controller
{
    /**
      * returns the volunteers
      *
      * @Route("/{id}/volunteers")
      * @Method("GET")
      * @Security("has_role('ROLE_USER')")
      */
    public function users(Request $request,Department $department)
    {
        // $authentication=...?

        if(!$this->isGranted('ROLE_USER'))
        {
            $response = new Response(
                'You may not request this ressource',
                Response::HTTP_FORBIDDEN,
                array('content-type' => 'text/plain')
            );
        }

        if($department==null)
        {
            $response = new Response(
                'The ressource has not been found',
                Response::HTTP_NOT_FOUND,
                array('content-type' => 'text/plain')
            );
        }

        $commitments = $department->getCommitments();
        $companions = $department->getCompanions();

        $data = array();
        foreach ($commitments as $commitment) {
            $user = array(
                'id' => $commitment->getUser()->getId(),
                'surname' => $commitment->getUser()->getSurname(),
                'forename'=> $commitment->getUser()->getForename(),
            );
            array_push($data,$user);
        }

        $response = new JsonResponse();

        $response->setData($data);
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
