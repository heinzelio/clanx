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
        $user = $this->getUser();
        $missingProfileData = '';
        if(!$user->getForename()){
            $missingProfileData = "Vorname";
        }
        if(!$user->getSurname()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Nachname");
        }
        if(!$user->getStreet()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Strasse");
        }
        if(!$user->getCity()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Wohnort");
        }
        if(!$user->getZip()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Postleitzahl");
        }
        if(!$user->getCountry()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Land");
        }
        if(!$user->getDateOfBirth()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Geburtsdatum");
        }
        if(!$user->getPhone()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Telefonnummer");
        }

        return $this->render('dashboard/index.html.twig', array(
            'ShowProfileUpdate' => $missingProfileData!=null||$missingProfileData!="",
            'MissingProfilData' => $missingProfileData
        ));
    }
    private function joinTxt($firstPart, $secondPart, $delimiter=", "){
        if($firstPart){
            return $firstPart.$delimiter.$secondPart;
        }
        return $secondPart;
    }
}

?>
