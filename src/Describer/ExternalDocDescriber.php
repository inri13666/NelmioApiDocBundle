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

use EXSyst\Component\Swagger\Swagger;

class ExternalDocDescriber implements DescriberInterface
{
    /** @var array|callable */
    private $externalDoc;

    /** @var bool */
    private $overwrite;

    /**
     * @param array|callable $externalDoc
     * @param bool $overwrite
     */
    public function __construct($externalDoc, $overwrite = false)
    {
        $this->externalDoc = $externalDoc;
        $this->overwrite = $overwrite;
    }

    /**
     * @param Swagger $api
     */
    public function describe(Swagger $api)
    {
        $externalDoc = $this->getExternalDoc();
        $api->merge($externalDoc, $this->overwrite);
    }

    /**
     * @return mixed
     */
    private function getExternalDoc()
    {
        if (is_callable($this->externalDoc)) {
            return call_user_func($this->externalDoc);
        }

        return $this->externalDoc;
    }
}
