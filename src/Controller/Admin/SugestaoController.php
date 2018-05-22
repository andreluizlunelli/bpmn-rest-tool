<?php
/**
 * User: André Lunelli <andre@microton.com.br>
 * Date: 01/11/2017
 */

namespace andreluizlunelli\BpmnRestTool\Controller\Admin;

use Psr\Container\ContainerInterface;
use Rodizio\Model\Entity\Sugestao;
use Rodizio\System\Database;

class SugestaoController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->entityManager = Database::getEm();
    }

    public function sugestoes(\Slim\Http\Request $request, \Slim\Http\Response $response, $args)
    {
        /**
         * Adicionar uma flag na sugestão de `pedidoAtendido` algo assim pra poder filtrar e buscar somente as sugestões não atendidas
         */
        $args['sugestoesEnviadas'] = $this->entityManager->getRepository(Sugestao::class)->findAll();
        return $this->container->get('view')->render($response, 'admin/sugestao.twig', $args);
    }
}