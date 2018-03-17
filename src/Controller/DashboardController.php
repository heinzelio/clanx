<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\RedirectInfo;
use App\Entity\Mail;
use App\Service\IAuthorizationService;
use App\Service\ISettingsService;

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
    public function indexAction(Request $request, IAuthorizationService $auth, ISettingsService $settings)
    {
        $user = $this->getUser();
        $missingProfileData = '';
        if(!$user->getForename()){
            $missingProfileData = "Vorname";
        }
        if(!$user->getSurname()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Nachname");
        }
        // there is no need for this at the moment.
        // if(!$user->getStreet()){
        //     $missingProfileData = $this->joinTxt($missingProfileData,"Strasse");
        // }
        // if(!$user->getCity()){
        //     $missingProfileData = $this->joinTxt($missingProfileData,"Wohnort");
        // }
        // if(!$user->getZip()){
        //     $missingProfileData = $this->joinTxt($missingProfileData,"Postleitzahl");
        // }
        // if(!$user->getCountry()){
        //     $missingProfileData = $this->joinTxt($missingProfileData,"Land");
        // }
        if(!$user->getDateOfBirth()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Geburtsdatum");
        }
        if(!$user->getPhone()){
            $missingProfileData = $this->joinTxt($missingProfileData,"Telefonnummer");
        }

        $showAdminRegistrationSwitch=false;
        $adminRegistrationInfoText='';
        $adminRegistrationButtonIcon='';
        if($auth->MayChangeSettings()){
            $showAdminRegistrationSwitch=true;
            if ($settings->canRegister()) {
                $adminRegistrationInfoText='Neue Benutzer zugelassen.';
                $adminRegistrationButtonIcon='fa-toggle-on';
            }else {
                $adminRegistrationInfoText='Es dürfen sich zur Zeit keine neuen Benutzer auf der Datenbank registrieren.';
                $adminRegistrationButtonIcon='fa-toggle-off';
            }
        }

        return $this->render('dashboard/index.html.twig', array(
            'ShowAdminRegistrationSwitch' => $showAdminRegistrationSwitch,
            'AdminRegistrationInfoText' => $adminRegistrationInfoText,
            'AdminRegistrationButtonIcon' => $adminRegistrationButtonIcon,
            'ShowAssociationMembershipRequest' => !$user->getIsAssociationMember(),
            'ShowProfileUpdate' => $missingProfileData!=null||$missingProfileData!="",
            'MissingProfilData' => $missingProfileData,
            'username' => $user->getUsername(),
        ));
    }

    private function joinTxt($firstPart, $secondPart, $delimiter=", "){
        if($firstPart){
            return $firstPart.$delimiter.$secondPart;
        }
        return $secondPart;
    }
    /**
    * @Route("/becomeMember", name="send_membership_request")
    * @Method("GET")
    * @Security("has_role('ROLE_USER')")
    */
    public function sendMembershipRequest(Request $request)
    {
        $session = $request->getSession();

        $mailData = new Mail();
        $user =$this->getUser();
        $text = "Vorname: ". $user->getForename()
            . "\r\nNachname: ". $user->getSurname()
            . "\r\nStrasse: ".$user->getStreet()
            . "\r\nOrt: ".$user->getZip()." ".$user->getCity()
            . "\r\nEMail: ".$user->getEmail()
            . "\r\n"
            . "\r\nGruss: ".(string)$user;

        $mailData->setSubject("Ich möchte Vereinsmitglied werden!")
             ->setSender($this->getUser()->getEmail())
             ->setRecipient('verein@clanx.ch')
             ->setText($text)
             ;

        $session->set(Mail::SESSION_KEY, $mailData);

        $backLink = new RedirectInfo();
        $backLink->setRouteName('dashboard_index')
              ->setArguments(null)
              ;

        $session->set(RedirectInfo::SESSION_KEY, $backLink);

        return $this->redirectToRoute('mail_edit');
    }
}

?>
