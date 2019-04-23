<?php

namespace andreluizlunelli\BpmnRestTool\Controller;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\BpmnBuilderSplitSubprocess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\GetAllElementTypeSubprocess;
use andreluizlunelli\BpmnRestTool\Model\Entity\BpmnEntity;
use andreluizlunelli\BpmnRestTool\Model\Entity\BpmnPiece;
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
                $metadataBuilder = new BpmnMetadataBuilder($projectEntity);
                $allSubProcess = (new GetAllElementTypeSubprocess($metadataBuilder->buildMetadata()))->all();
                $builder = new BpmnBuilderSplitSubprocess();
                $listXmlPieces = $builder->buildXmlsSplited($allSubProcess);

                $user = $this->getUserLoggedin();
                $bpmn = (new BpmnEntity())
                    ->setUser($user)
                    ->setFileInformed(file_get_contents($splFile->getPathname()))
                    ->setName(uniqid() . "-" . pathinfo($f->getClientFilename())['filename']);
                ;

                /** @var BpmnPiece $piece */
                foreach ($listXmlPieces as $piece) {
                    $bpmn->addBpmnPiece($piece->setBpmn($bpmn));
                }

                $user->addBpmn($bpmn);

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
        $bpmn = $args['bpmn'];
        $subProcess = $args['subProcess'];

        $user = $this->getUserLoggedin();

        $bpmnEntityCollection = $user->getBpmnList()->filter(function (/** @var BpmnEntity $item */ $item) use ($bpmn) {
            return $bpmn === $item->getName() ? $item : false;
        });

        $bpmnEntity = $bpmnEntityCollection->first();

        $bpmnSubprocessCollection = $bpmnEntity->getGeneratedPieces()->filter(function (/** @var BpmnPiece $item */ $item) use ($subProcess) {
            return $subProcess === explode(', Dt.início', $item->getName())[0] ? $item : false;
        });

        return $response
            ->withStatus(200)
            ->write($bpmnSubprocessCollection->first()->getXml());
    }

    public function bpmn(Request $request, Response $response, $args)
    {
        return $this->view()->render($response, './bpmn.twig', $args);
    }

    public function bpmnList(Request $request, Response $response, $args)
    {
        $args['flashMessage'] = $this->message();
        $args['files'] = array_map(function (/** @var $item BpmnEntity*/ $item) {
            return [
                'name' => $item->getName()
                , 'pieces' => array_map(function ($p) {
                    return [
                        'name' => explode(', Dt.início', $p->getName())[0]
                    ];
                }, array_reverse($item->getGeneratedPieces()->toArray()))
            ];
        }, $this->ordenarDataMaior($this->getUserLoggedin()
            ->getBpmnList()
            ->toArray()
        ));

        if (count($args['files']) < 1)
            $this->message()->addMessageNow('info', "Você não possue nenhuma bpmn, clique <a class='alert-link' href='{$this->route()->pathFor('carregarXmlProject')}'>aqui</a> para começar");

        return $this->view()->render($response, './bpmn-list.twig', $args);
    }

    private function ordenarDataMaior(array $bpmnList): array
    {
        uasort($bpmnList, function (BpmnEntity $a, BpmnEntity $b) {
            if ($a->getCreatedAt() == $b->getCreatedAt())
                return 0;
            return ($a->getCreatedAt() > $b->getCreatedAt()) ? -1 : 1;
        });
        return $bpmnList;
    }

}
