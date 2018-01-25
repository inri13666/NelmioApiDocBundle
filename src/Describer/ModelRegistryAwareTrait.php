<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\Describer;

use Akuma\Component\ApiDoc\Model\ModelRegistry;

trait ModelRegistryAwareTrait
{
    /**
     * @var ModelRegistry
     */
    private $modelRegistry;

    public function setModelRegistry(ModelRegistry $modelRegistry)
    {
        $this->modelRegistry = $modelRegistry;
    }
}
