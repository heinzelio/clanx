<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use AppBundle\Entity\User;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;
use AppBundle\Entity\Commitment;
use AppBundle\Entity\LegacyUser;

/**
 * Dashboard controller.
 * @Route("/superadmin")
 */
class SuperAdminController extends Controller
{
    /**
    * @Route("/data/upload", name="admin_upload_data")
    * @Method({"GET","POST"})
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */
    public function uploadAction(Request $request)
    {
        $uploadForm = $this->createUploadForm();
        $uploadForm->handleRequest($request);
        if ($uploadForm->isSubmitted() && $uploadForm->isValid())
        {
            $file = $uploadForm->get('file')->getData();
            $legacyUsers = $this->parseFile($file);

            $em = $this->getDoctrine()->getManager();
            foreach($legacyUsers as $lu){
                $lu->setMail(strtolower($lu->getMail()));
                $em->persist($lu);
            }
            $em->flush();
            return $this->render('superadmin/showUploaded.html.twig',array(
                'legacyUsers' => $legacyUsers,
            ));
        }
        return $this->render('superadmin/upload.html.twig',array(
            'upload_form' => $uploadForm->createView()
        ));
    }

    /**
    * @Route("/make/test/data", name="admin_make_test_data")
    * @Method({"GET"})
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */
    public function makeTestDataAction(Request $request)
    {
        $roadNames = array('Bahnhof','Post','Lang','Schmid','Metzger'
            ,'Bank','Schiller','Markt', 'Edison','Tannen','Schönholz'
            ,'Zürcher','Schweizer',);
        $roadSuffixes = array('strasse','gasse','weg','platz'
            ,'gässlein','treppe','graben');
        $cityNames = array('Oberschön','Unterschön','Vorderholz'
            ,'Hinterholz','Flach','Berg','Neuberg','Bach','Altbach');
        $citySuffixes = array('wil','hof','kon','ikon','au','berg'
            ,'enen','ingen','eschwil');
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $legacyRepo = $em->getRepository('AppBundle:LegacyUser');

        // run chuncks of 10 to avoid timeout
        $count=0;

        foreach ($userRepo->findAll() as $u) {
            // Canonical mail adress is automatically updated
            // before persisting data. (fosUserBundle cares about it.)
            // Password will also be encoded.
            if($this->getUser()->getId()==$u->getId()){
                continue; // do not change the current user.
            }
            $newUserName = 'clanxer'.$u->getId();
            $newMail = $newUserName.'@mailinator.com';
            $newPhone = '0'.rand(76,79).' '.rand(100,999).' '
                        .rand(10,99).' '.rand(10,99);
            $newZip = rand(1000,9999);
            $newStreet = $roadNames[array_rand($roadNames)].$roadSuffixes[array_rand($roadSuffixes)].' '.rand(1,99);
            $newCity = $cityNames[array_rand($cityNames)].$citySuffixes[array_rand($citySuffixes)];

            $mail = $u->getEmail();
            $legacyUser = $legacyRepo->findOneByMail($mail);
            if($legacyUser)
            {
                $legacyUser->setMail($newMail);
            }
            $u->setEmail($newMail);
            $u->setUserName($newUserName);
            $u->setPhone($newPhone);
            $u->setStreet($newStreet);
            $u->setZip($newZip);
            $u->setCity($newCity);
            $u->setPlainPassword('1234');
            if($legacyUser)
            {
                $em->persist($legacyUser);
            }
            $em->persist($u);

            $count++;
            if ($count>=10) {
                $em->flush();
                $count=0;
            }
        }
        $em->flush();
        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to upload a CSV file
     * @return \Symfony\Component\Form\Form The form
     */
    private function createUploadForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_upload_data'))
            ->add('file', FileType::class, array('label' => 'Legacy users (CSV only)'))
            ->setMethod('POST')
            ->getForm()
        ;
    }

    private function parseFile($file)
    {
        if(!$file) return null;

        $rowIdx = 0;
        $legacyUsers = array();
        if (($fhandle = fopen($file, "r")) !== FALSE)
        {
            // parse each line as csv
            while (($data = fgetcsv($fhandle, 1000, ";")) !== FALSE)
            {
                if(!$data[7]) continue; // ignore records without mail. (we need that later)
                // get each field in a line
                // Vorname;Nachname;Adresse;Postleitzahl;Ort;Land;
                // Telefon;E-Mail-Adresse;Geburtsdatum clean;Geschlecht;
                // Dein Beruf;Ressort 2015
                $legacyUser = new LegacyUser();
                $legacyUser->setForename($data[0]);
                $legacyUser->setSurname($data[1]);
                $legacyUser->setAddress($data[2]);
                $legacyUser->setZip($data[3]);
                $legacyUser->setCity($data[4]);
                $legacyUser->setCountry($data[5]);
                $legacyUser->setPhone($data[6]);
                $legacyUser->setMail($data[7]);
                if($data[8])
                {
                    $date = \DateTime::createFromFormat('d.m.Y', $data[8]);
                    // from the doc: "Returns a new DateTime instance or FALSE on failure."
                    // SERIOUSLY, PHP??? ... returns apples or pears or cars or planes, just as he likes... :(
                    if($date)
                    {
                        $legacyUser->setDateOfBirth($date);
                    }
                }
                switch ($data[9]) {
                    case 'Mann':
                        $legacyUser->setGender('M');
                        break;
                    case 'Frau':
                        $legacyUser->setGender('F');
                        break;
                    default:
                        break;
                }
                $legacyUser->setOccupation($data[10]);
                $legacyUser->setLastDepartment($data[11]);

                $legacyUsers[$rowIdx] = $legacyUser;
                $rowIdx++;
            }
            fclose($fhandle);
        }
        return $legacyUsers;
    }
}

?>
