<?php

namespace Akuma\Component\ApiDoc\Helper;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;

class DoctrineManagerRegistryHelper implements ManagerRegistry
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultConnectionName()
    {
        return 'default';
    }

    /**
     * @inheritDoc
     */
    public function getConnection($name = null)
    {
        return $this->entityManager->getConnection();
    }

    /**
     * @inheritDoc
     */
    public function getConnections()
    {
        return ['default' => $this->entityManager->getConnection()];
    }

    /**
     * @inheritDoc
     */
    public function getConnectionNames()
    {
        return ['default'];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultManagerName()
    {
        return ['default'];
    }

    /**
     * @inheritDoc
     */
    public function getManager($name = null)
    {
        return $this->entityManager;
    }

    /**
     * @inheritDoc
     */
    public function getManagers()
    {
        return ['default' => $this->entityManager];
    }

    /**
     * @inheritDoc
     */
    public function resetManager($name = null)
    {
        $this->entityManager->clear();
    }

    /**
     * @inheritDoc
     */
    public function getAliasNamespace($alias)
    {
        return $alias;
    }

    /**
     * @inheritDoc
     */
    public function getManagerNames()
    {
        return ['default'];
    }

    /**
     * @inheritDoc
     */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->entityManager->getRepository(ClassUtils::getRealClass($persistentObject));
    }

    /**
     * @inheritDoc
     */
    public function getManagerForClass($class)
    {
        return $this->entityManager;
    }
}
