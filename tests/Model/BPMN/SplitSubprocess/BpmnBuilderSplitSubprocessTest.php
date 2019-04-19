<?php

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\SplitSubprocess;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\BpmnBuilderSplitSubprocess;
use andreluizlunelli\BpmnRestTool\Model\BPMN\SplitSubprocess\GetAllElementTypeSubprocess;
use andreluizlunelli\BpmnRestTool\Model\Entity\BpmnEntity;
use andreluizlunelli\BpmnRestTool\Model\Entity\User;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use andreluizlunelli\BpmnRestTool\System\Database;
use PHPUnit\Framework\TestCase;

class BpmnBuilderSplitSubprocessTest extends TestCase
{
    public function excluir()
    {
        $em = Database::getEntityManager();
        $em->createQuery('DELETE '.BpmnEntity::class)->execute();
    }

    public function testa()
    {
        $this->excluir();
        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../../bpmn_xml/initiatingplanningclosing.xml'));

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $rootEl = $bpmn->buildMetadata();

        $allSubProcess = (new GetAllElementTypeSubprocess($rootEl))->all();
        $builder = new BpmnBuilderSplitSubprocess();
        $listXmlString = $builder->buildXmlsSplited($allSubProcess);

        $em = Database::getEntityManager();
        $repo = $em->getRepository(User::class);
        $user = $repo->find(1);
        $i = 1;
        foreach ($listXmlString as $item) {
            $bpmn = (new BpmnEntity())
                ->setFileInformed($i)
                ->setGeneratedFile($item)
                ->setName("teste$i");

            $user->addBpmn($bpmn);
            $em->persist($user);

            $i++;
        }
        $em->flush();
    }

}