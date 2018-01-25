<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc;

use Akuma\Component\ApiDoc\Describer\DescriberInterface;
use Akuma\Component\ApiDoc\Describer\ModelRegistryAwareInterface;
use Akuma\Component\ApiDoc\Model\ModelRegistry;
use Akuma\Component\ApiDoc\ModelDescriber\ModelDescriberInterface;
use EXSyst\Component\Swagger\Swagger;
use Psr\Cache\CacheItemPoolInterface;

final class ApiDocGenerator
{
    /** @var Swagger */
    private $swagger;

    /** @var array|Describer\DescriberInterface[] */
    private $describers;

    /** @var array|ModelDescriber\ModelDescriberInterface[] */
    private $modelDescribers;

    /** @var CacheItemPoolInterface */
    private $cacheItemPool;

    /**
     * @param DescriberInterface[] $describers
     * @param ModelDescriberInterface[] $modelDescribers
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(array $describers, array $modelDescribers, CacheItemPoolInterface $cacheItemPool = null)
    {
        $this->describers = $describers;
        $this->modelDescribers = $modelDescribers;
        $this->cacheItemPool = null;
    }

    /**
     * @param DescriberInterface $describer
     *
     * @return $this
     */
    public function addDescriber(DescriberInterface $describer)
    {
        $this->describers[] = $describer;

        return $this;
    }

    /**
     * @param ModelDescriberInterface $modelDescriber
     *
     * @return $this
     */
    public function addModelDescriber(ModelDescriberInterface $modelDescriber)
    {
        $this->modelDescribers[] = $modelDescriber;

        return $this;
    }

    /**
     * @return Swagger
     */
    public function generate()
    {
        if (null !== $this->swagger) {
            return $this->swagger;
        }

        if ($this->cacheItemPool) {
            $item = $this->cacheItemPool->getItem('swagger_doc');
            if ($item->isHit()) {
                return $this->swagger = $item->get();
            }
        }

        $this->swagger = new Swagger();
        $modelRegistry = new ModelRegistry($this->modelDescribers, $this->swagger);
        foreach ($this->describers as $describer) {
            if ($describer instanceof ModelRegistryAwareInterface) {
                $describer->setModelRegistry($modelRegistry);
            }

            $describer->describe($this->swagger);
        }
        $modelRegistry->registerDefinitions();

        if (isset($item)) {
            $this->cacheItemPool->save($item->set($this->swagger));
        }

        return $this->swagger;
    }
}
