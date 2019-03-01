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
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends ControllerBase
{
    public function carregarXmlProject(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, './enviar.twig', $args);
    }

    public function postCarregarXmlProject(Request $request, Response $response, $args)
    {
        $files = $request->getUploadedFiles();

        /** @var UploadedFileInterface $f */
        foreach ($files as $f) {
            $f->moveTo(getcwd() . '/public/project-informado/' . $f->getClientFilename());

            $projectEntity = (new ProjectMapper())
                ->map(new \SplFileObject(getcwd() . '/public/project-informado/' . $f->getClientFilename()));

            $bpmn = new BpmnMetadataBuilder($projectEntity);

            $builder = new BpmnBuilder($bpmn->buildMetadata());

            $xml = $builder->buildXml();

            file_put_contents(getcwd() . '/public/bpmn-geradas/' . current(explode('.', $f->getClientFilename())).'.bpmn', $xml);
        }

        return $response->withRedirect('/bpmn');
    }

    public function bpmn(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, './bpmn.twig', $args);
    }

    public function bpmnList(Request $request, Response $response, $args)
    {
        foreach (new \DirectoryIterator(getcwd() . '/public/bpmn-geradas') as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            if ($fileInfo->getFilename() == '.gitkeep') continue;

            $file['name'] = $fileInfo->getFilename();
            $file['location'] = "/bpmn-geradas/{$fileInfo->getFilename()}";

            $args['files'][] = $file;
        }
        return $this->view()->render($response, './bpmn-list.twig', $args);
    }

}