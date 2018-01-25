<?php
use Akuma\Component\ApiDoc\Annotation\ApiDoc;

class AcmeController
{
    /**
     * @ApiDoc(
     *           requirements = {
     *                {
     *                   "name"="name",
     *                   "datatype"="array",
     *                   "requirements"="\w+",
     *                   "description" = "description for this parameter"
     *          }
     *            },
     *       )
     */
    public function indexPage()
    {
    }
}
