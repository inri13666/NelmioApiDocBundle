<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\Model;

use Symfony\Component\PropertyInfo\Type;

final class Model
{
    /** @var Type */
    private $type;

    /** @var array|null|\string[] */
    private $groups;

    /**
     * @param Type $type
     * @param string[]|null $groups
     */
    public function __construct(Type $type, array $groups = null)
    {
        $this->type = $type;
        $this->groups = $groups;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string[]|null
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function getHash()
    {
        return md5(serialize([$this->type, $this->groups]));
    }
}
