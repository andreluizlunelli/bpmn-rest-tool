<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 18:38
 */

namespace andreluizlunelli\BpmnRestTool\Model\Authorization;

use andreluizlunelli\BpmnRestTool\Model\Entity\User;
use Doctrine\ORM\EntityManager;
use Zend\Session\SessionManager;

class AuthorizationUser
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * AuthorizationUser constructor.
     * @param EntityManager $em
     * @param SessionManager $sessionManager
     */
    public function __construct(EntityManager $em, SessionManager $sessionManager)
    {
        $this->em = $em;
        $this->sessionManager = $sessionManager;
    }

    public function getUser(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    public function start(string $email, string $password): void
    {
        $user = $this->getUser($email);

        if (empty($user))
            throw new \InvalidArgumentException('Ops! Esse usuário não existe :(');

        if ( ! $user->passwordVerify($password))
            throw new \InvalidArgumentException('Senha errada. Tente novamente por gentileza.');

        $this->sessionManager->start();
        $this->sessionManager->getStorage()->setMetadata('user', $user->jsonSerialize());
    }

}