<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 25/02/2019
 * Time: 10:56
 */

namespace andreluizlunelli\BpmnRestTool\System;

class SystemConst
{

    /**
     * @return mixed
     */
    public static function getSettings()
    {
        return require __DIR__ . '/../settings.php';
    }

    public static function getDateTimeFormat(): string
    {
        return self::getSettings()['settings']['date_formats']['date_time_format'];
    }

}