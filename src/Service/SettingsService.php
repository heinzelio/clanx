<?php
namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Entity\Setting;

class SettingsService implements ISettingsService
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var App\Entity\Setting
     */
    protected $setting;

    public function __construct(
        EntityManager $em
    )
    {
        $this->em = $em;
        $repository = $em->getRepository(Setting::class);
        $this->setting = $repository->findAll()[0];
    }

    public function canRegister()
    {
        return $this->setting->getCanRegister();
    }

    public function toggleCanRegister()
    {
        $newValue = !$this->setting->getCanRegister();
        $this->setting->setCanRegister($newValue);
        $this->em->persist($this->setting);
        $this->em->flush();
        return $newValue;
    }
}
