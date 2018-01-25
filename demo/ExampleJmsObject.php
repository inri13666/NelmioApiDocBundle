<?php

use JMS\Serializer\Annotation as Serializer;

class ExampleJmsObject
{
    /**
     * @var string
     * @Serializer\Expose()
     * @Serializer\Type("string")
     */
    public $gretta;

    /**
     * @var int
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    public $grettaInt;

    /**
     * @var int
     * @Serializer\Exclude()
     */
    public $grettaExcluded;
}
