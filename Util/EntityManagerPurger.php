<?php

namespace Visca\Bundle\DoctrineBundle\Util;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Class EntityManagerPurger.
 */
final class EntityManagerPurger
{
    /**
     * @param EntityManager $entityManager The entity manager to purge
     */
    public function purge(EntityManager $entityManager)
    {
        $purger = new ORMPurger($entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();

        /** @var ClassMetaData[] $metadataArray */
        $metadataArray = $entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($metadataArray as $metadata) {
            if (($metadata->isInheritanceTypeSingleTable()
                    && $metadata->name != $metadata->rootEntityName)
                || $metadata->isMappedSuperclass
            ) {
                continue;
            }

            $tableName = $metadata->table['name'];
            $query = "ALTER TABLE `$tableName` AUTO_INCREMENT = 1";
            $entityManager->getConnection()->executeUpdate($query);

            $entityManager->clear();
        }
    }
}
