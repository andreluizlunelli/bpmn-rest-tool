<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 17/05/2018
 * Time: 19:39
 */

namespace andreluizlunelli\BpmnRestTool\Model\Project;

use andreluizlunelli\BpmnRestTool\Model\Traits\AttrElement;
use QueryPath\DOMQuery;

class ProjectTask
{
    use AttrElement;

    private $outlineLevel;

    /**
     * @var DOMQuery
     */
    public $domQuery;

    public function __construct(string $name, int $outlineLevel)
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->outlineLevel = $outlineLevel;
    }
    
    public function getOutlineLevel(): int
    {
        return $this->outlineLevel;
    }

}
