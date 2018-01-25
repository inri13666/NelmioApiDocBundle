<?php

/*
 * This file is part of the NelmioApiDocBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\Extractor;

/**
 * Interface for annotations providers.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
interface AnnotationsProviderInterface
{
    /**
     * Returns an array ApiDoc annotations.
     *
     * @return \Akuma\Component\ApiDoc\Annotation\ApiDoc[]
     */
    public function getAnnotations();
}
