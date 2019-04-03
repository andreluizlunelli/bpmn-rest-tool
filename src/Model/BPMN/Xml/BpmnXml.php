<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 14:15
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

class BpmnXml
{
    private $xml = [];
    private $sequences = [];

    /**
     * BpmnXml constructor.
     * @param array $xml
     * @param array $sequences
     */
    public function __construct(array $xml, array $sequences)
    {
        $this->xml = $xml;
        $this->sequences = $sequences;
    }

    public function getXml(): array
    {
        return $this->xml;
    }

    public function getSequences(): array
    {
        return $this->sequences;
    }

    /**
     * @param array $xml
     * @return BpmnXml
     */
    public function setXml(array $xml): self
    {
        $this->xml = $xml;
        return $this;
    }

    /**
     * @param array $sequences
     * @return BpmnXml
     */
    public function setSequences(array $sequences): self
    {
        $this->sequences = $sequences;
        return $this;
    }

}
