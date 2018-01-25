##### example-cli.php
```php
<?php

require_once dirname(__FILE__) . '/../vendor/autoload.php';
require_once 'AcmeController.php';
require_once 'ExampleObject.php';

$collectionBuilder = new \Symfony\Component\Routing\RouteCollectionBuilder();
$collectionBuilder
    ->add('/', '\AcmeController::indexPage')
    ->setMethods('POST');

if (class_exists('\JMS\Serializer\Annotation\Type')) {
    require_once 'ExampleJmsObject.php';
    $collectionBuilder
        ->add('/jms', '\AcmeController::indexJmsPage')
        ->setMethods('POST');
}

$helper = new \Akuma\Component\ApiDoc\Helper\ApiDocHelper($collectionBuilder->build());
$spec = $helper->getApiDocGenerator()->generate()->toArray();

echo json_encode($spec);
```

##### AcmeController
```php
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
```

##### ExampleJmsObject
```php
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
```

##### ExampleObject
```php
<?php

class ExampleObject
{
    /**
     * @var string
     */
    public $gretta;

    /**
     * @var int
     */
    public $grettaInt;

    /**
     * @var int
     */
    public $grettaExcluded;
}
```

### Example outputs

#### Without JMS Serializer
```json
{
    "swagger": "2.0",
    "info": {
        "title": "",
        "version": "0.0.0"
    },
    "paths": {
        "\/": {
            "post": {
                "responses": {
                    "200": {
                        "description": "Success",
                        "schema": {
                            "$ref": "#\/definitions\/ExampleObject"
                        }
                    }
                }
            }
        }
    },
    "definitions": {
        "ExampleObject": {
            "properties": {
                "gretta": {
                    "type": "string"
                },
                "grettaInt": {
                    "type": "integer"
                },
                "grettaExcluded": {
                    "type": "integer"
                }
            },
            "type": "object"
        }
    }
}
```

#### With JMS Serializer
```json
{
    "swagger": "2.0",
    "info": {
        "title": "",
        "version": "0.0.0"
    },
    "paths": {
        "\/": {
            "post": {
                "responses": {
                    "200": {
                        "description": "Success",
                        "schema": {
                            "$ref": "#\/definitions\/ExampleObject"
                        }
                    }
                }
            }
        },
        "\/jms": {
            "post": {
                "responses": {
                    "200": {
                        "description": "Success",
                        "schema": {
                            "$ref": "#\/definitions\/ExampleJmsObject"
                        }
                    }
                }
            }
        }
    },
    "definitions": {
        "ExampleObject": {
            "type": "object"
        },
        "ExampleJmsObject": {
            "properties": {
                "gretta": {
                    "type": "string"
                },
                "gretta_int": {
                    "type": "integer"
                }
            },
            "type": "object"
        }
    }
}
```
