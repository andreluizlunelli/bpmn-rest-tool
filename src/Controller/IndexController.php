<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 31/05/2018
 * Time: 11:45
 */

namespace andreluizlunelli\BpmnRestTool\Controller;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\Entity\BpmnEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends ControllerBase
{
    use UserLoggedin;

    public function carregarXmlProject(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, './enviar.twig', $args);
    }

    public function postCarregarXmlProject(Request $request, Response $response, $args)
    {
        $files = $request->getUploadedFiles();

        /** @var UploadedFileInterface $f */
        foreach ($files as $f) {
            try {
                $f->moveTo(getcwd() . '/public/project-informado/' . $f->getClientFilename());

                $splFile = new \SplFileObject(getcwd() . '/public/project-informado/' . $f->getClientFilename());

                $projectEntity = (new ProjectMapper())->map($splFile);

                $bpmn = new BpmnMetadataBuilder($projectEntity);
                $builder = new BpmnBuilder($bpmn->buildMetadata());
                $xml = $builder->buildXml();

                $bpmnEntity = (new BpmnEntity())
                    ->setFileInformed(file_get_contents($splFile->getPathname()))
                    ->setGeneratedFile($xml)
                    ->setName(uniqid() . "-" . $f->getClientFilename());

                $user = $this->getUserLoggedin();
                $user->addBpmn($bpmnEntity);
                $this->em()->persist($user);
                $this->em()->flush();
            } finally {
                $delete = $splFile->getPathname();
                $splFile = null;
                unlink($delete);
            }
        }

        return $response->withRedirect($this->route()->pathFor('index'));
    }

    public function bpmn(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, './bpmn.twig', $args);
    }

    public function bpmnList(Request $request, Response $response, $args)
    {
        $args['files'] = array_map(function (/** @var $item BpmnEntity*/ $item) {
            return ['name' => $item->getName() ];
        }, $this->getUserLoggedin()
            ->getBpmnList()
            ->toArray()
        );

        return $this->view()->render($response, './bpmn-list.twig', $args);
    }

}