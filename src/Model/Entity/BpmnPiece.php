<?php

namespace andreluizlunelli\BpmnRestTool\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BpmnPiece
 * @package andreluizlunelli\BpmnRestTool\Model\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="bpmn_piece")
 */
class BpmnPiece
{
    use EntityTrait;

    /**
     * @var BpmnEntity
     *
     * @ORM\ManyToOne(targetEntity="BpmnEntity", inversedBy="generatedPieces", cascade={"persist"})
     */
    private $bpmn;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $xml;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * BpmnPiece constructor.
     * @param string $xml
     * @param string $name
     */
    public function __construct(string $xml, string $name)
    {
        $this->xml = $xml;
        $this->name = $name;
    }

    /**
     * @param BpmnEntity $bpmn
     * @return BpmnPiece
     */
    public function setBpmn(BpmnEntity $bpmn): self
    {
        $this->bpmn = $bpmn;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getXml(): string
    {
        return $this->xml;
    }

}