<?php
/**
 * Created by PhpStorm.
 * User: André Luiz
 * Date: 22/08/2017
 * Time: 13:21
 */

namespace andreluizlunelli\BpmnRestTool\System;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\SchemaTool;

class Database
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private static $entityManager = null;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            self::$entityManager = self::createEntityManager();
        }

        return self::$entityManager;
    }

    /**
     * @return EntityManager
     */
    public static function createEntityManager(): EntityManager
    {
        $dbconfig = App::getApp()->getContainer()->get('settings')['doctrine'];

        $devMode = $dbconfig['dev_mode'];

        /**
         * Diretório(s) onde estão as entidades do projeto
         */
        $paths = $dbconfig['entity_path'];

        $config = Setup::createAnnotationMetadataConfiguration($paths, $devMode);

        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        $config->setMetadataDriverImpl($driver);


        /**
         * Cria o EntityManager com a configuração alterada
         */
        return self::$entityManager = EntityManager::create($dbconfig, $config);
    }

    /**
     * Alias for Database::getEntityManager
     *
     * @return EntityManager
     */
    public static function getEm(): EntityManager
    {
        return self::getEntityManager();
    }

    /**
     * Cria as tabelas
     * também pode ser utilizado o comando <code>vendor/bin/doctrine orm:schema-tool:create</code>
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/tools.html#database-schema-generation
     */
    public static function createTables()
    {
        $em = self::getEntityManager();

        $allMetadataEntities = $em->getMetadataFactory()->getAllMetadata();

        $listMetaData = [];
        /** @var ClassMetadata $metaData */
        array_walk($allMetadataEntities, function ($metaData) use (&$listMetaData) {

            if (! $metaData->isMappedSuperclass)
                $listMetaData[] = $metaData;
        });

        $st = new SchemaTool($em);

        $st->createSchema($listMetaData);
    }

    /**
     * Trunca as tabelas do sistema
     * Utilizado nos testes
     * @see http://doctrine-orm.readthedocs.io/en/latest/reference/batch-processing.html#dql-delete
     */
    public static function truncateTables()
    {
        $em = self::getEntityManager();

        $allMetadataEntities = $em->getMetadataFactory()->getAllMetadata();

        /**
         * Põe a licença como primeira a ser excluido pois é a classe que tem relacionamento com usuário e proprietário
         */
        /** @var ClassMetadata $first */
        foreach ($allMetadataEntities as $key => $first) {
            if ($first->name == 'MT\Model\EmitLicense\License') {
                unset($allMetadataEntities[$key]);
                array_unshift($allMetadataEntities, $first);
            }
        }

        /** @var ClassMetadata $metaData */
        foreach ($allMetadataEntities as $metaData) {
            if ($metaData->isMappedSuperclass)
                continue;

            $q = $em->createQuery("delete from $metaData->rootEntityName");
            $q->execute();
        }
        $em->clear();
    }
}
