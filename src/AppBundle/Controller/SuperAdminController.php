<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\Event;
use AppBundle\Entity\Department;
use AppBundle\Entity\Commitment;

/**
 * Dashboard controller.
 * @Route("/superadmin")
 */
class SuperAdminController extends Controller
{
    /**
    * @Route("/data/generate", name="admin_generate_data")
    * @Method("GET")
    * @Security("has_role('ROLE_SUPER_ADMIN')")
    */
    public function indexAction(Request $request)
    {
        $flashbag = $request->getSession()->getFlashBag();
        $em = $this->getDoctrine()->getManager();
        $ctUser = $this->getUser();
        $usrRepo = $em->getRepository('AppBundle:User');
        $evtRepo = $em->getRepository('AppBundle:Event');
        $dptRepo = $em->getRepository('AppBundle:Department');
        $cmtRepo = $em->getRepository('AppBundle:Commitment');
        // clear commitments
        $this->clearAll($cmtRepo,$em,$flashbag);
        // clear departments
        $this->clearAll($dptRepo,$em,$flashbag);
        // clear events
        $this->clearAll($evtRepo,$em,$flashbag);
        // clear users
        $this->clearAll($usrRepo,$em,$flashbag,$ctUser->getId()); // except the logged in User

        $usrAdmin = (new User())->setUsername('admin')->setEmail('admin@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_ADMIN');
        $dptLeadBar  = (new User())->setUsername('bar')->setEmail('bar@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $dptLeadBau  = (new User())->setUsername('bau')->setEmail('bau@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $barClanxer1 = (new User())->setUsername('clanxer1')->setEmail('clanxer1@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $barClanxer2 = (new User())->setUsername('clanxer2')->setEmail('clanxer2@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $barClanxer3 = (new User())->setUsername('clanxer3')->setEmail('clanxer3@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $barClanxer4 = (new User())->setUsername('clanxer4')->setEmail('clanxer4@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $barClanxer5 = (new User())->setUsername('clanxer5')->setEmail('clanxer5@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $bauClanxer6 = (new User())->setUsername('clanxer6')->setEmail('clanxer6@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $bauClanxer7 = (new User())->setUsername('clanxer7')->setEmail('clanxer7@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');
        $bauClanxer8 = (new User())->setUsername('clanxer8')->setEmail('clanxer8@mailinator.com')->setEnabled(true)->setPlainPassword('1234')->addRole('ROLE_USER');

        $em->persist($usrAdmin);
        $em->persist($dptLeadBar);
        $em->persist($dptLeadBau);
        $em->persist($barClanxer1);
        $em->persist($barClanxer2);
        $em->persist($barClanxer3);
        $em->persist($barClanxer4);
        $em->persist($barClanxer5);
        $em->persist($bauClanxer6);
        $em->persist($bauClanxer7);
        $em->persist($bauClanxer8);
        $flashbag->add('success', 'Hinzugefügt: 1 Admin, 2 Ressortleiter, 8 User');

        $evtClanx15 = (new Event())->setName("Clanx '15")->setDate(new \DateTime("2015-08-28 00:00:00.0"))->setSticky(true);
        $evtTour15 = (new Event())->setName("Vereinsreise '15")->setDate(new \DateTime("2015-09-28 00:00:00.0"))->setSticky(false);
        $evtClanx16 = (new Event())->setName("Clanx '16")->setDate(new \DateTime("2016-08-26 00:00:00.0"))->setSticky(true);
        $evtTour16 = (new Event())->setName("Vereinsreise '16")->setDate(new \DateTime("2016-09-28 00:00:00.0"))->setSticky(false);

        $em->persist($evtClanx15);
        $em->persist($evtTour15);
        $em->persist($evtClanx16);
        $em->persist($evtTour16);
        $flashbag->add('success', 'Hinzugefügt: 2 vergangene Events, 2 anstehende Events');

        // set departments for clanx '15
        $dptBar1_15 = (new Department())->setName('Hautbar')->setRequirement('mind 18ni')->setChiefUser($dptLeadBar)->setDeputyUser($barClanxer1)->setEvent($evtClanx15);
        $dptBar2_15 = (new Department())->setName('Bar Bar')->setRequirement('mind 18ni')->setChiefUser($dptLeadBar)->setDeputyUser($barClanxer2)->setEvent($evtClanx15);
        $dptBau_15 = (new Department())->setName('Bau')->setChiefUser($dptLeadBau)->setDeputyUser($bauClanxer6)->setEvent($evtClanx15);
        // set departments for vereinsreise '15
        $dptReise_15 = (new Department())->setName('Reiseleitung 15')->setChiefUser($barClanxer5)->setEvent($evtTour15);

        $em->persist($dptBar1_15);
        $em->persist($dptBar2_15);
        $em->persist($dptBau_15);
        $em->persist($dptReise_15);
        $flashbag->add('success', 'Hinzugefügt: 4 Ressorts für 2 vergangene Events');

        // set departments for clanx '16
        $dptBar1_16 = (new Department())->setName('Hautbar')->setRequirement('mind 18ni')->setChiefUser($dptLeadBar)->setDeputyUser($barClanxer3)->setEvent($evtClanx16);
        $dptBar2_16 = (new Department())->setName('Bar Bar')->setRequirement('mind 18ni')->setChiefUser($dptLeadBar)->setDeputyUser($barClanxer4)->setEvent($evtClanx16);
        $dptBau_16 = (new Department())->setName('Bau')->setChiefUser($dptLeadBau)->setDeputyUser($bauClanxer7)->setEvent($evtClanx16);
        // set departments for vereinsreise '16
        $dptReise_16 = (new Department())->setName('Reiseleitung 16')->setChiefUser($barClanxer5)->setEvent($evtTour16);

        $em->persist($dptBar1_16);
        $em->persist($dptBar2_16);
        $em->persist($dptBau_16);
        $em->persist($dptReise_16);
        $flashbag->add('success', 'Hinzugefügt: 4 Ressorts für 2 anstehende Events');


        // add some commitments
        $cmt=(new Commitment())->setUser($barClanxer1)->setEvent($evtClanx15)->setDepartment($dptBar1_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer2)->setEvent($evtClanx15)->setDepartment($dptBar2_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer3)->setEvent($evtClanx15)->setDepartment($dptBar1_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer4)->setEvent($evtClanx15)->setDepartment($dptBar1_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer5)->setEvent($evtClanx15)->setDepartment($dptBar1_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($bauClanxer6)->setEvent($evtClanx15)->setDepartment($dptBau_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($bauClanxer7)->setEvent($evtClanx15)->setDepartment($dptBau_15);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($bauClanxer8)->setEvent($evtClanx15)->setDepartment($dptBau_15);
        $em->persist($cmt);

        $cmt=(new Commitment())->setUser($barClanxer4)->setEvent($evtTour15)->setDepartment($dptReise_15);
        $em->persist($cmt);

        $cmt=(new Commitment())->setUser($barClanxer1)->setEvent($evtClanx16)->setDepartment($dptBar1_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer2)->setEvent($evtClanx16)->setDepartment($dptBar2_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer3)->setEvent($evtClanx16)->setDepartment($dptBar1_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer4)->setEvent($evtClanx16)->setDepartment($dptBau_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($barClanxer5)->setEvent($evtClanx16)->setDepartment($dptBar1_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($bauClanxer6)->setEvent($evtClanx16)->setDepartment($dptBar1_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($bauClanxer7)->setEvent($evtClanx16)->setDepartment($dptBau_16);
        $em->persist($cmt);
        $cmt=(new Commitment())->setUser($bauClanxer8)->setEvent($evtClanx16)->setDepartment($dptBau_16);
        $em->persist($cmt);

        $cmt=(new Commitment())->setUser($barClanxer4)->setEvent($evtTour16)->setDepartment($dptReise_16);
        $em->persist($cmt);

        $flashbag->add('success', 'Hinzugefügt: Ä Schwetti Commitments');




        $em->flush();
        return $this->redirectToRoute('dashboard_index');
    }

    private function clearAll($repository, $entityManager, $flashbag = null, $exceptId = 0)
    {
        if(!$repository) return;
        if(!$entityManager) return;
        $counter = 0;
        $name=null;
        foreach ($repository->FindAll() as $entity) {
            $name = get_class($entity);
            if($entity->getId()!=$exceptId)
            {
                $entityManager->remove($entity);
                $counter++;
            }
        }

        $entityManager->flush();
        if(!$flashbag) return;
        if(!$name) return;
        $flashbag->add('success', 'Gelöscht: '.$counter.' x '.$name);
    }
}

?>
