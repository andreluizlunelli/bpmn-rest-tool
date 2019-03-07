<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 06/03/2019
 * Time: 20:47
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use andreluizlunelli\BpmnRestTool\Model\Entity\User;

trait UserLoggedin
{
    public function getUserLoggedin(): ?User
    {
        $sm = $this->container->get('SessionManager');
        $userArr = $sm->getStorage()->getMetadata('user');
        return $this->em()->getRepository(User::class)->find($userArr['id']);
    }

}