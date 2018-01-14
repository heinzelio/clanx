<?php
namespace App\Service\Flash;

use Symfony\Component\HttpFoundation\Session\Session;
use App\Service;

class FlashService
{
    private $danger;
    private $warning;
    private $info;
    private $success;

    private $prototypes;

    public function __construct($session)
    {
        $this->danger = new DangerFlash();
        $this->warning = new WarningFlash();
        $this->info = new InfoFlash();
        $this->success = new SuccessFlash();

        $this->prototypes = array(
            $this->danger->getKey() => $this->danger,
            $this->warning->getKey() => $this->warning,
            $this->info->getKey() => $this->info,
            $this->success->getKey() => $this->success,
        );
    }

    public function getBootstrapClass($flashKey)
    {
        $this->testAndThrowKeyExists($flashKey);
        return $this->prototypes[$flashKey]->getBootstrapClass();
    }

    public function getTranslatorKey($flashKey)
    {
        $this->testAndThrowKeyExists($flashKey);
        return $this->prototypes[$flashKey]->getTranslatorKey();
    }

    private function testAndThrowKeyExists($givenKey)
    {
        if(!array_key_exists ( $givenKey , $this->prototypes ))
        {
            throw new Exception("The flash key '" .$givenKey."' is not supported");
        }
    }
}
