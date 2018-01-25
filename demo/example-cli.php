<?php
/**
 * USAGE:
 *  php example-html.php api:doc:dump --format=json
 *  php example-html.php api:doc:dump --format=markdown
 *  php example-html.php api:doc:dump --format=html
 *
 * Example Output:
 * JSON: {"others":[{"method":"POST","uri":"\/","requirements":{"name":{"datatype":"array","requirements":"\\w+","description":"description for this parameter"}},"https":false,"authentication":false,"authenticationRoles":[],"deprecated":false}]}
 */
require_once  dirname(__FILE__) . '/../vendor/autoload.php';
require_once  'AcmeController.php';

$application = new \Symfony\Component\Console\Application();

$collectionBuilder = new \Symfony\Component\Routing\RouteCollectionBuilder();
$collectionBuilder
    ->add('/', '\AcmeController::indexPage')
    ->setMethods('POST');

$application->addCommands([
    new \Akuma\Component\ApiDoc\Command\DumpCommand($collectionBuilder->build()),
]);

$application->run();
