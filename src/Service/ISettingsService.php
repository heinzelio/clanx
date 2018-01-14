<?php
namespace App\Service;

interface ISettingsService
{
    public function canRegister();

    public function toggleCanRegister();
}
