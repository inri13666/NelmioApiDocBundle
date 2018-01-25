<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\ModelDescriber;

use Akuma\Component\ApiDoc\Model\Model;
use EXSyst\Component\Swagger\Schema;

interface ModelDescriberInterface
{
    /**
     * @param Model $model
     * @param Schema $schema
     */
    public function describe(Model $model, Schema $schema);

    /**
     * @param Model $model
     *
     * @return bool
     */
    public function supports(Model $model);
}
