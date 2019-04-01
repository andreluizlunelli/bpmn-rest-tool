<?php

namespace andreluizlunelli\BpmnRestTool\Controller;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\Entity\BpmnEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Body;
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
                    ->setName(uniqid() . "-" . pathinfo($f->getClientFilename())['filename']);

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

    public function fetchBpmn(Request $request, Response $response, $args)
    {
        $fileName = $args['fileName'];

        $user = $this->getUserLoggedin();

        $bpmnEntityCollection = $user->getBpmnList()->filter(function (/** @var BpmnEntity $item */ $item) use ($fileName) {
            return $fileName === $item->getName() ? $item : false;
        });

        return $response
            ->withStatus(200)
            ->write($bpmnEntityCollection->first()->getGeneratedFile());
    }

    public function bpmn(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, './bpmn.twig', $args);
    }

    public function bpmnList(Request $request, Response $response, $args)
    {
        $args['flashMessage'] = $this->message();
        $args['files'] = array_map(function (/** @var $item BpmnEntity*/ $item) {
            return ['name' => $item->getName()];
        }, $this->getUserLoggedin()
            ->getBpmnList()
            ->toArray()
        );

        if (count($args['files']) < 1)
            $this->message()->addMessageNow('info', "Você não possue nenhuma bpmn, clique <a class='alert-link' href='{$this->route()->pathFor('carregarXmlProject')}'>aqui</a> para começar");

        return $this->view()->render($response, './bpmn-list.twig', $args);
    }

}
