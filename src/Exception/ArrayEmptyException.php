<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 14/06/2018
 * Time: 21:02
 */

namespace andreluizlunelli\BpmnRestTool\Exception;

use Throwable;

class ArrayEmptyException extends \Exception
{
    public function __construct(string $message = "O array informado não pode ser vazio", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}