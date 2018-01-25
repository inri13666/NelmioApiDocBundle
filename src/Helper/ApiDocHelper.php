<?php

namespace Akuma\Component\ApiDoc\Helper;

use Akuma\Component\ApiDoc\Annotation\Model;
use Akuma\Component\ApiDoc\ApiDocGenerator;
use Akuma\Component\ApiDoc\Describer\SwaggerPhpDescriber;
use Akuma\Component\ApiDoc\ModelDescriber\JMSModelDescriber;
use Akuma\Component\ApiDoc\ModelDescriber\ModelDescriberInterface;
use Akuma\Component\ApiDoc\ModelDescriber\ObjectModelDescriber;
use Akuma\Component\ApiDoc\ModelDescriber\SwaggerPropertyAnnotationReader;
use Akuma\Component\ApiDoc\Util\ControllerReflector;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\AnnotationDriver as JmsAnnotationDriver;
use JMS\Serializer\Metadata\Driver\DoctrineTypeDriver;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
use Metadata\Driver\DriverChain;
use Metadata\MetadataFactory;
use Swagger\Annotations\Info;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Routing\RouteCollection;

class ApiDocHelper
{
    /** @var ApiDocGenerator */
    protected $apiDocGenerator;

    /** @var EntityManager */
    protected $doctrine;

    /**
     * @param RouteCollection $routeCollection
     * @param EntityManager|null $doctrine
     */
    public function __construct(RouteCollection $routeCollection, EntityManager $doctrine = null)
    {
        //Register JSM annotations, cuz PSR-0 doesn't allow to do it
        if (class_exists('JMS\Serializer\SerializerBuilder')) {
            $reflection = new \ReflectionClass(SerializerBuilder::class);
            AnnotationRegistry::registerAutoloadNamespace(
                'JMS\Serializer\Annotation',
                dirname($reflection->getFileName(), 3)
            );
        }

        AnnotationRegistry::registerLoader(function ($class) {
            if (stripos('Swagger\Annotations', $class) == 0) {
                $reflection = new \ReflectionClass(Info::class);
                $fileToLoad = str_replace(
                        '\\',
                        DIRECTORY_SEPARATOR,
                        str_replace('Swagger\Annotations', dirname($reflection->getFileName()), $class)
                    ) . '.php';
                if (is_readable($fileToLoad)) {
                    require_once $fileToLoad;

                    return true;
                }

                return false;
            }
        });

        AnnotationRegistry::registerLoader(function ($class) {
            if (stripos('Akuma\Component\ApiDoc\Annotation', $class) == 0) {
                $reflection = new \ReflectionClass(Model::class);
                $fileToLoad =
                    str_replace(
                        '\\',
                        DIRECTORY_SEPARATOR,
                        str_replace('Akuma\Component\ApiDoc\Annotation', dirname($reflection->getFileName()), $class)
                    ) . '.php';
                if (is_readable($fileToLoad)) {
                    require_once $fileToLoad;

                    return true;
                }

                return false;
            }
        });

        //Register Doctrine ORM annotations, just for sure
        if (class_exists('\Doctrine\ORM\Mapping\Driver\AnnotationDriver', true)) {
            $reflection = new \ReflectionClass(AnnotationDriver::class);
            $file = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php';
            if (is_readable($file)) {
                AnnotationRegistry::registerFile($file);
            }
        }

        $this->doctrine = $doctrine;
        $this->routeCollection = $routeCollection;
    }

    /**
     * @return ApiDocGenerator
     */
    public function getApiDocGenerator()
    {
        if (!$this->apiDocGenerator) {
            $this->apiDocGenerator = new ApiDocGenerator(
                $this->getDefaultDescribers(),
                $this->getDefaultModelDescribers()
            );
        }

        return $this->apiDocGenerator;
    }

    protected function getDefaultDescribers()
    {
        return [
            new SwaggerPhpDescriber(
                $this->getRouteCollection(),
                $this->getControllerReflector(),
                new AnnotationReader()
            ),
        ];
    }

    /**
     * @return array|ModelDescriberInterface[]
     */
    protected function getDefaultModelDescribers()
    {
        // a full list of extractors is shown further below
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();

        // array of PropertyListExtractorInterface
        $listExtractors = [$reflectionExtractor];

        // array of PropertyTypeExtractorInterface
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];

        if ($this->doctrine) {
            $doctrineExtractor = new DoctrineExtractor($this->doctrine->getMetadataFactory());
            array_unshift($listExtractors, $doctrineExtractor);
            array_unshift($typeExtractors, $doctrineExtractor);
        }

        // array of PropertyDescriptionExtractorInterface
        $descriptionExtractors = [$phpDocExtractor];

        // array of PropertyAccessExtractorInterface
        $accessExtractors = [$reflectionExtractor];

        $propertyInfo = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors
        );

        $modelDescribers = [
            new ObjectModelDescriber(
                $propertyInfo,
                $this->getSwaggerPropertyAnnotationReader()
            ),
        ];

        if (class_exists('\JMS\Serializer\Annotation\Type')) {
            array_unshift($modelDescribers, new JMSModelDescriber(
                $this->getJmsMetadataFactory(),
                $this->getJmsNamingStrategy(),
                $this->getSwaggerPropertyAnnotationReader()
            ));
        }

        return $modelDescribers;
    }

    /**
     * @return null|RouteCollection
     */
    protected function getRouteCollection()
    {
        return $this->routeCollection;
    }

    /**
     * @return ControllerReflector
     */
    protected function getControllerReflector()
    {
        return new ControllerReflector();
    }

    /**
     * @return MetadataFactory|object
     */
    private function getJmsMetadataFactory()
    {
        $chain = new DriverChain([
            new JmsAnnotationDriver(new AnnotationReader()),
        ]);

        if ($this->doctrine) {
            $registry = new DoctrineManagerRegistryHelper($this->doctrine);
            $chain = new DoctrineTypeDriver($chain, $registry);
        }

        return new MetadataFactory($chain);
    }

    /**
     * @return SerializedNameAnnotationStrategy|object
     */
    private function getJmsNamingStrategy()
    {
        return new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
    }

    /**
     * @return SwaggerPropertyAnnotationReader|object
     */
    private function getSwaggerPropertyAnnotationReader()
    {
        return new SwaggerPropertyAnnotationReader(new AnnotationReader());
    }
}
