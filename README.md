Akuma Api Doc Component
==================

Based on [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle)

This component allows to use functionality of NelmioApiDocBundle, but not only with symfony.
It can be used with any framework or pure php


##### Silex 1.2 Example

###### app/config/routes.yml
```yml
acme.api_doc_route:
    path: /api-doc
    methods: ['POST']
    defaults: { _controller: 'Acme\DocsController::indexAction' }

acme.api_doc_route:
    path: /api-doc
    methods: ['POST']
    defaults: { _controller: 'Acme\ApiController::anyAction' }
```

###### DocsController.php
```php
<?php

namespace Acme;

use Akuma\Component\ApiDoc\Annotation\ApiDoc;
use Akuma\Component\ApiDoc\Util\ApiDocHelper;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;

class DocsController
{
    /**
     * @param string $view
     *
     * @return Response
     */
    public function indexAction($view = ApiDoc::DEFAULT_VIEW)
    {
        $reflection = new \ReflectionClass(ApiDoc::class);
        AnnotationRegistry::registerFile($reflection->getFileName());

        $helper = new ApiDocHelper();
        $extractor = $helper->getApiDocExtractor();
        $extractor->setRouteCollection($this->getRoutes());
        $extractedDoc = $extractor->all($view);
        $htmlContent = $helper->getFormatter('html')->format($extractedDoc);

        $response = (new Response($htmlContent, 200, array('Content-Type' => 'text/html')));
        $response->send();
        exit();
    }

    /**
     * @return RouteCollection
     */        
    protected function getRoutes()
    {
        //Obtain or fill routes collection   
    }
}
```

###### DocsController.php
```php
<?php

namespace Acme;

use Akuma\Component\ApiDoc\Annotation\ApiDoc;

class ApiController
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
    public function indexAction()
    {
        //Do some cool stuff
    }
}
```

###### console.php
```php
<?php

// Application
$app = new \Silex\Application();

$this->app['routes'] = $this->app->extend(
    'routes',
    function (\Symfony\Component\Routing\RouteCollection $routes) {

        $loader = new Symfony\Component\Routing\Loader\YamlFileLoader(
            new \Symfony\Component\Config\FileLocator(__DIR__ . '/../app/config')
        );
        $collection = $loader->load('routes.yml');
        $routes->addCollection($collection);

        return $routes;
    });

//.... Silex Application configuration
// for orm.em see here http://dflydev.com/projects/doctrine-orm-service-provider/
// for twig see here https://silex.symfony.com/doc/1.3/providers/twig.html

$application = new Application();
$application->addCommands([
    new \Akuma\Component\ApiDoc\Command\DumpCommand(),
]);

$application->run();
```
