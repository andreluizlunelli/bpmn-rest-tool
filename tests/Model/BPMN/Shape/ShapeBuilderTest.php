<?php
/**
 * Criado por: andre.lunelli
 * Date: 08/04/2019 - 16:21
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Shape;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\Entity\BpmnEntity;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use andreluizlunelli\BpmnRestTool\System\App;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShapeBuilderTest extends TestCase
{
    public function testeLayout()
    {
        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../../bpmn_xml/initiatingplanningclosing.xml'));

        /*
        $bpmn = new BpmnMetadataBuilder($projectEntity);
        $bpmnXmlBuilder = new BpmnXmlBuilder();
        $rootXml = $bpmnXmlBuilder->build($bpmn->buildMetadata());
        $getAllSequences = new GetAllSequences($rootXml);
        $sequences = $getAllSequences->all();
        $calcShape = new CalcShape();
        $shapeBuilder = new ShapeBuilder($rootXml, $sequences, $calcShape);
        $xmlArray = $shapeBuilder->xml(); */

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $this->rootEl = $bpmn->buildMetadata();

        $builder = new BpmnBuilder($this->rootEl);

        $xml = $builder->buildXml();

        /** @var EntityManager $em */
        $em = App::getApp()->getContainer()->get('em');

        $bpmnEntity = $em->getRepository(BpmnEntity::class)->find(25);

//        $bpmnEntity = new BpmnEntity();
        $bpmnEntity->setFileInformed('abc');
        $bpmnEntity->setGeneratedFile($xml);
        $bpmnEntity->setName('abc');

        $em->persist($bpmnEntity);
        $em->flush();

    }

}
