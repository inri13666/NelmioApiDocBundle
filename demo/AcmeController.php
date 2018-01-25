<?php

class AcmeController
{
    /**
     * @\Swagger\Annotations\Response(
     *   response="200",
     *   description="Success",
     *   @\Akuma\Component\ApiDoc\Annotation\Model(type=\ExampleObject::class),
     * )
     *
     */
    public function indexPage()
    {
    }

    /**
     * @\Swagger\Annotations\Response(
     *   response="200",
     *   description="Success",
     *   @\Akuma\Component\ApiDoc\Annotation\Model(type=\ExampleJmsObject::class),
     * )
     *
     */
    public function indexJmsPage()
    {
    }
}
