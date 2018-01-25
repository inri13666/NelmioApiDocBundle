<?php

/*
 * This file is part of the NelmioApiDocBundle package.
 *
 * (c) Nelmio
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\SwaggerPhp;

use Akuma\Component\ApiDoc\Annotation\Model as ModelAnnotation;
use Akuma\Component\ApiDoc\Model\Model;
use Akuma\Component\ApiDoc\Model\ModelRegistry;
use Swagger\Analysis;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Items;
use Swagger\Annotations\Parameter;
use Swagger\Annotations\Response;
use Swagger\Annotations\Schema;
use Symfony\Component\PropertyInfo\Type;

/**
 * Resolves the path in SwaggerPhp annotation when needed.
 *
 * @internal
 */
final class ModelRegister
{
    /** @var ModelRegistry */
    private $modelRegistry;

    /**
     * @param ModelRegistry $modelRegistry
     */
    public function __construct(ModelRegistry $modelRegistry)
    {
        $this->modelRegistry = $modelRegistry;
    }

    /**
     * @param Analysis $analysis
     */
    public function __invoke(Analysis $analysis)
    {
        $modelsRegistered = [];
        foreach ($analysis->annotations as $annotation) {
            if ($annotation instanceof Response) {
                $annotationClass = Schema::class;
            } elseif ($annotation instanceof Parameter) {
                if ('array' === $annotation->type) {
                    $annotationClass = Items::class;
                } else {
                    $annotationClass = Schema::class;
                }
            } elseif ($annotation instanceof Schema) {
                $annotationClass = Items::class;
            } else {
                continue;
            }

            $model = null;
            foreach ($annotation->_unmerged as $unmerged) {
                if ($unmerged instanceof ModelAnnotation) {
                    $model = $unmerged;

                    break;
                }
            }

            if (null === $model || !$model instanceof ModelAnnotation) {
                continue;
            }

            if (!is_string($model->type)) {
                // Ignore invalid annotations, they are validated later
                continue;
            }

            $ref = $this->modelRegistry->register(new Model($this->createType($model->type), $model->groups));
            $parts = explode('/', $ref);
            $modelsRegistered[end($parts)] = true;

            $annotation->merge([new $annotationClass([
                'ref' => $ref,
            ])]);

            // It is no longer an unmerged annotation
            foreach ($annotation->_unmerged as $key => $unmerged) {
                if ($unmerged === $model) {
                    unset($annotation->_unmerged[$key]);

                    break;
                }
            }
            $analysis->annotations->detach($model);
        }

        foreach ($modelsRegistered as $model => $v) {
            $analysis->annotations->attach(new Definition(['definition' => $model]));
        }
    }

    /**
     * @param string $type
     *
     * @return Type
     */
    private function createType($type)
    {
        if ('[]' === substr($type, -2)) {
            return new Type(Type::BUILTIN_TYPE_ARRAY, false, null, true, null, $this->createType(substr($type, 0, -2)));
        }

        return new Type(Type::BUILTIN_TYPE_OBJECT, false, $type);
    }
}
