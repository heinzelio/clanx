<?php
namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use AppBundle\Service\Authorization;

class MailBuilderService
{
    /**
     * Authorization service
     * @var Authorization
     */
    private $auth;

    public function __construct(
        Authorization $auth
    )
    {
        $this->auth = $auth;
    }

}

?>
