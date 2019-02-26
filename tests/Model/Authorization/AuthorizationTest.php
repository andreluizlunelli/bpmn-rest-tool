<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 09:50
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\Authorization;

use andreluizlunelli\BpmnRestTool\Model\Entity\User;
use PHPUnit\Framework\TestCase;
use Zend\Session\Config\StandardConfig;
use Zend\Session\SessionManager;
use Zend\Session\Storage\ArrayStorage;
use Zend\Session\Storage\SessionArrayStorage;

class AuthorizationTest extends TestCase
{

    public function testSession()
    {
        $user = new User('nome', 'email');

        $manager = new SessionManager(new StandardConfig(), new ArrayStorage());
        $manager->getStorage()->setMetadata('user', $user->toArray());
        $manager->start();
    }

}