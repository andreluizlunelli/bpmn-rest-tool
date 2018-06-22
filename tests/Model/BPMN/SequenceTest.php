<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 21/06/2018
 * Time: 19:33
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN;

use andreluizlunelli\BpmnRestTool\Model\BPMN\Sequence;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    public function testCriarSequence()
    {
        $sourceRef = 'umid';
        $targetRef = 'outroid';
        $sequence = new Sequence($sourceRef, $targetRef);


    }
}