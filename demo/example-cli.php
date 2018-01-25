<?php
/**
 * USAGE:
 *  php example-cli.php
 *
 * Example Output:
 * JSON WITHOUT JMS:
 * {"swagger":"2.0","info":{"title":"","version":"0.0.0"},"paths":{"\/":{"post":{"responses":{"200":{"description":"Success","schema":{"$ref":"#\/definitions\/ExampleObject"}}}}}},"definitions":{"ExampleObject":{"properties":{"gretta":{"type":"string"},"grettaInt":{"type":"integer"},"grettaExcluded":{"type":"integer"}},"type":"object"}}}
 * JSON WITH JMS:
 * {"swagger":"2.0","info":{"title":"","version":"0.0.0"},"paths":{"\/":{"post":{"responses":{"200":{"description":"Success","schema":{"$ref":"#\/definitions\/ExampleObject"}}}}},"\/jms":{"post":{"responses":{"200":{"description":"Success","schema":{"$ref":"#\/definitions\/ExampleJmsObject"}}}}}},"definitions":{"ExampleObject":{"type":"object"},"ExampleJmsObject":{"properties":{"gretta":{"type":"string"},"gretta_int":{"type":"integer"}},"type":"object"}}}
 */
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
