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
      * @Route("/{id}/volunteers", name="api_volunteers_of_department")
      * @Method("GET")
      */
    public function users(Request $request,Department $department)
    {
        // $authentication=...?
        // It's all done!!! (How cool is that?)

        if(!$this->isGranted('ROLE_USER'))
        {
            $response = new Response(
                'You may not request this ressource',
                Response::HTTP_FORBIDDEN,
                array('content-type' => 'text/plain')
            );
            return $response;
        }

        $commitments = $department->getCommitments();
        $companions = $department->getCompanions();
        //TODO Also the companions!

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

    /**
      * returns the shifts
      *
      * @Route("/{id}/shifts", name="api_shifts_of_department")
      * @Method("GET")
      */
    public function shifts(Request $request,Department $department)
    {
        if(!$this->isGranted('ROLE_USER'))
        {
            $response = new Response(
                'You may not request this ressource',
                Response::HTTP_FORBIDDEN,
                array('content-type' => 'text/plain')
            );
            return $response;
        }

        $shifts = $department->getShifts();

        $data = array();
        foreach ($shifts as $shift) {
            $s = array(
                'id' => $shift->getId(),
                'start' => $shift->getStart()->format('H:i'),
                'end'=> $shift->getEnd()->format('H:i'),
                'name'=> (string)$shift,
            );
            array_push($data,$s);
        }

        $response = new JsonResponse();

        $response->setData($data);
        $response->setStatusCode(Response::HTTP_OK);
        return $response;
    }
}
