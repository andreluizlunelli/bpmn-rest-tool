<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 13:02
 */

namespace andreluizlunelli\BpmnRestTool\Cmd;

require __DIR__ . '/../../vendor/autoload.php';

use andreluizlunelli\BpmnRestTool\Model\Entity\User;
use Doctrine\ORM\EntityManager;
use League\CLImate\CLImate;
use andreluizlunelli\BpmnRestTool\System\App;

class CmdUser
{
    public static function call(\Slim\App $app, array $args)
    {
        $climate = new CLImate();
        $climate->arguments->description("Criar um usuário do sistema");
        $climate->arguments->add([
            'nome' => [
                'prefix'       => 'n',
                'longPrefix'   => 'nome',
                'description'  => 'Nome',
                'required'=> true
            ],
            'email' => [
                'prefix'       => 'e',
                'longPrefix'   => 'email',
                'description'  => 'Email',
                'required'=> true
            ],
            'senha' => [
                'prefix'      => 's',
                'longPrefix'  => 'senha',
                'description' => 'Senha',
                'defaultValue' => null
            ],
        ]);

        try {
            $climate->arguments->parse($args);

            $name = trim((string)$climate->arguments->get('nome'));
            $email = trim((string)$climate->arguments->get('email'));
            $password = trim((string)$climate->arguments->get('senha'));

            $user = new User($name, $email, empty($password) ? null : $password);

            /** @var EntityManager $em */
            $em = $app->getContainer()->get('em');
            $em->persist($user);
            $em->flush();

            $climate->green('Usuário inserido com sucesso');

        } catch (\Exception $exception) {
            $climate->usage($args);
            $climate->to('error')->red($exception->getMessage());
        }

    }
}

CmdUser::call(App::getApp(), $argv);