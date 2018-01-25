<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\Util;

final class ControllerReflector
{
    /** @var array */
    private $controllers = [];

    /**
     * Returns the ReflectionMethod for the given controller string.
     *
     * @param string $controller
     *
     * @return \ReflectionMethod|null
     */
    public function getReflectionMethod($controller)
    {
        $callable = $this->getClassAndMethod($controller);
        if (null === $callable) {
            return null;
        }

        list($class, $method) = $callable;
        try {
            return new \ReflectionMethod($class, $method);
        } catch (\ReflectionException $e) {
            // In case we can't reflect the controller, we just
            // ignore the route
        }
    }

    /**
     * @param $controller
     *
     * @return array|null
     */
    public function getReflectionClassAndMethod($controller)
    {
        $callable = $this->getClassAndMethod($controller);
        if (null === $callable) {
            return null;
        }

        list($class, $method) = $callable;
        try {
            return [new \ReflectionClass($class), new \ReflectionMethod($class, $method)];
        } catch (\ReflectionException $e) {
            // In case we can't reflect the controller, we just
            // ignore the route
        }

        return null;
    }

    /**
     * @param $controller
     *
     * @return array|mixed|null
     */
    private function getClassAndMethod($controller)
    {
        if (isset($this->controllers[$controller])) {
            return $this->controllers[$controller];
        }

        if (preg_match('#(.+)::([\w]+)#', $controller, $matches)) {
            $class = $matches[1];
            $method = $matches[2];
        } elseif (class_exists($controller)) {
            $class = $controller;
            $method = '__invoke';
        } else {
            if (preg_match('#(.+):([\w]+)#', $controller, $matches)) {
                $controller = $matches[1];
                $method = $matches[2];
            }
        }

        if (!isset($class) || !isset($method)) {
            $this->controllers[$controller] = null;

            return null;
        }

        return $this->controllers[$controller] = [$class, $method];
    }
}
