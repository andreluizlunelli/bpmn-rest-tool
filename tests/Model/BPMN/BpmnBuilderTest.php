<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 19/06/2018
 * Time: 00:13
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\BpmnMetadataBuilder;
use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;
use andreluizlunelli\BpmnRestTool\Model\Project\ProjectMapper;
use PHPUnit\Framework\TestCase;

class BpmnBuilderTest extends TestCase
{
    /**
     * @var TypeElementAbstract
     */
    private $rootEl;

    protected function setUp()
    {
        parent::setUp();

        $projectEntity = (new ProjectMapper())
            ->map(new \SplFileObject('../../bpmn_xml/BpmnMetadataBuilderTest_testBuildMetadataCom3Elementos.xml'));

        $bpmn = new BpmnMetadataBuilder($projectEntity);

        $this->rootEl = $bpmn->buildMetadata();
    }

    public function testCriarXmlBpmn()
    {
        $builder = new BpmnBuilder($this->rootEl);

        $xml = $builder->buildXml();

        self::assertNotEmpty($xml);

        echo "\n";
        echo $xml;
        echo "\n";
    }

}