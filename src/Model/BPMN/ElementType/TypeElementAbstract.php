<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 20:48
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType;

use andreluizlunelli\BpmnRestTool\Model\Traits\AttrElement;

abstract class TypeElementAbstract implements TypeElementInterface
{
    use AttrElement;

    /**
     * @var array <TypeElementInterface>
     */
    protected $outgoing;

    public function __construct(string $id, string $name, array $outgoing = [])
    {
        $this->id = "{$this->getNameWithoutNamespace()}_$id";
        $this->name = $name;
        $this->outgoing = $outgoing;
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->getNameWithoutNamespace()
            , 'id' => $this->id
            , 'name' => $this->name
            , 'outgoing' => array_map(function ($item) { return $item->jsonSerialize(); }, $this->outgoing)
        ];
    }

    /**
     * @return array
     */
    public function getOutgoing(): array
    {
        return $this->outgoing;
    }

    /**
     * @param array $outgoing
     * @return TypeElementAbstract
     */
    public function setOutgoing(array $outgoing): self
    {
        $this->outgoing = $outgoing;
        return $this;
    }

    public function addOutgoing(TypeElementInterface $element): self
    {
        array_push($this->outgoing, $element);
        return $this;
    }

    /**
     * @return mixed
     */
    private function getNameWithoutNamespace(): string
    {
        $arr = explode('\\', get_class($this));

        return array_pop($arr);
    }

}