<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class FilteredRouteCollectionBuilder
{
    /** @var array */
    private $pathPatterns;

    /**
     * @param array $pathPatterns
     */
    public function __construct(array $pathPatterns = [])
    {
        $this->pathPatterns = $pathPatterns;
    }

    /**
     * @param RouteCollection $routes
     *
     * @return RouteCollection
     */
    public function filter(RouteCollection $routes)
    {
        $filteredRoutes = new RouteCollection();
        foreach ($routes->all() as $name => $route) {
            if ($this->match($route)) {
                $filteredRoutes->add($name, $route);
            }
        }

        return $filteredRoutes;
    }

    /**
     * @param Route $route
     *
     * @return bool
     */
    private function match(Route $route)
    {
        foreach ($this->pathPatterns as $pathPattern) {
            if (preg_match('{' . $pathPattern . '}', $route->getPath())) {
                return true;
            }
        }

        return false;
    }
}
