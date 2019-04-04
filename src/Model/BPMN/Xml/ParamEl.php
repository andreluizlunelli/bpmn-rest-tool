<?php
/**
 * Criado por: andre.lunelli
 * Date: 03/04/2019 - 17:06
 */

namespace andreluizlunelli\BpmnRestTool\Model\BPMN\Xml;

use andreluizlunelli\BpmnRestTool\Model\BPMN\ElementType\TypeElementAbstract;

class ParamEl
{
    /**
     * @var TypeElementAbstract
     */
    private $prevEl;

    /**
     * @var TypeElementAbstract
     */
    private $actualEl;

    /**
     * @var TypeElementAbstract
     */
    private $nextEl;

    /**
     * ParamEl constructor.
     * @param TypeElementAbstract $prevEl
     * @param TypeElementAbstract $actualEl
     * @param TypeElementAbstract $nextEl
     */
    public function __construct(?TypeElementAbstract $prevEl, TypeElementAbstract $actualEl, ?TypeElementAbstract $nextEl)
    {
        $this->prevEl = $prevEl;
        $this->actualEl = $actualEl;
        $this->nextEl = $nextEl;
    }

    /**
     * @return TypeElementAbstract
     */
    public function getPrevEl(): ?TypeElementAbstract
    {
        return $this->prevEl;
    }

    /**
     * @return TypeElementAbstract
     */
    public function getActualEl(): ?TypeElementAbstract
    {
        return $this->actualEl;
    }

    /**
     * @return TypeElementAbstract
     */
    public function getNextEl(): ?TypeElementAbstract
    {
        return $this->nextEl;
    }

}
