<?php

require_once "vendor/autoload.php";

use andreluizlunelli\BpmnRestTool\System\Database;
use Symfony\Component\Console\Helper\HelperSet;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

$helperSet = new HelperSet(array(
    'em' => new EntityManagerHelper(Database::getEm())
));

return $helperSet;
