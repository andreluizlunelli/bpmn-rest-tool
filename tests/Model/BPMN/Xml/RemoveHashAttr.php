<?php
/**
 * Criado por: andre.lunelli
 * Date: 04/04/2019 - 16:46
 */

namespace andreluizlunelli\TestBpmnRestTool\Model\BPMN\Xml;

trait RemoveHashAttr
{
    public static function removeHashAttr(string $string)
    {
        self::removeAllIds('sequenceFlow_', $string);
        self::removeAllIds('StartEvent_', $string);
        self::removeAllIds('TaskActivity_', $string);
        self::removeAllIds('EndEvent_', $string);
        self::removeAllIds('SubProcess_', $string);
        return $string;
    }

    private static function removeAllIds(string $keySearch, string &$val): void
    {
        while (strlen(self::valReplace($val, $keySearch)) > 0) {
            $remove = self::valReplace($val, $keySearch);
            $val = str_replace($remove, '', $val);
        }
    }

    private static function valReplace(string $string, string $key): string
    {
        $pos = strpos($string, $key);

        if ( ! $pos)
            return '';

        //soma mais 13 da hash
        return substr($string, $pos, strlen($key) + 13);
    }
}
