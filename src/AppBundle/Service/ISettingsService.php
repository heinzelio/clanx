<?php
namespace AppBundle\Service;

interface ISettingsService
{
    public function canRegister();

    public function toggleCanRegister();
}
