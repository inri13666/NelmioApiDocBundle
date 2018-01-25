<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\RouteDescriber;

use EXSyst\Component\Swagger\Swagger;
use Symfony\Component\Routing\Route;

interface RouteDescriberInterface
{
    /**
     * @param Swagger $api
     * @param Route $route
     * @param \ReflectionMethod $reflectionMethod
     */
    public function describe(Swagger $api, Route $route, \ReflectionMethod $reflectionMethod);
}
