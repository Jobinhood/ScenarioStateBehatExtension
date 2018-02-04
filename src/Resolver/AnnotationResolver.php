<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Resolver;

use Doctrine\Common\Annotations\Reader;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateAutoload;
use Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateAutosave;
use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class AnnotationResolver
{
    /**
     * @var ScenarioStateInitializer
     */
    private $scenarioStateInitializer;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param ScenarioStateInitializer $scenarioStateInitializer
     * @param Reader                   $reader
     */
    public function __construct(ScenarioStateInitializer $scenarioStateInitializer, Reader $reader)
    {
        $this->scenarioStateInitializer = $scenarioStateInitializer;
        $this->reader = $reader;
    }

    /**
     * @param \ReflectionMethod $function
     * @param array $arguments
     *
     * @return array
     */
    public function resolve(\ReflectionMethod $function, array $arguments)
    {
        // No `@ScenarioStateAutoload` or '@ScenarioStateAutosave' annotations are found
        if ( null === $this->reader->getMethodAnnotation($function, ScenarioStateAutoload::class) &&
             null === $this->reader->getMethodAnnotation($function, ScenarioStateAutosave::class)) {
            return $arguments;
        }

        $paramKeys = array_map(function (\ReflectionParameter $element) {
            return $element->getName();
        }, $function->getParameters());

        // Prepare arguments from annotations
        /** @var ScenarioStateArgument[] $annotations */
        $annotations = $this->reader->getMethodAnnotations($function);
        foreach ($annotations as $annotation) {

            if ($annotation instanceof ScenarioStateAutosave) {
                throw new \Exception(
                    'Annotation ScenarioStateAutosave is not yet implemented. '.
                    'Please remove the annotation and save the items to the store '.
                    'via $this->store->add()'
                );
            }

            if ($annotation instanceof ScenarioStateAutoload) {

                if (! in_array($annotation->getArgument(), $paramKeys)) {
                    throw new \Exception(
                        sprintf(
                            '%s does not exist in existing function arguments: %s.',
                            $annotation->getArgument(),
                            implode(" | ", $paramKeys)
                        )
                    );
                }

                $storeKey = $arguments[$annotation->getArgument()];
                if (! $this->getStore()->has($storeKey)) {
                    throw new \Exception(
                        sprintf(
                            '%s store key does not exist in the store. Current store '.
                            'keys are %s.',
                            $arguments[$annotation->getArgument()],
                            implode(" | ", $this->getStore()->getKeys())
                        )
                    );

                }

                $storeKey = $arguments[$annotation->getArgument()];
                $arguments[$annotation->getArgument()] = $this->getStore()
                    ->get($storeKey);

            }
        }

        // Reorder arguments
        $params = [];
        foreach ($function->getParameters() as $parameter) {
            $name = $parameter->getName();
            $params[$name] = isset($arguments[$name]) ? $arguments[$name] : $arguments[$parameter->getPosition()];
        }

        return $params;
    }

    /**
     * @return Gorghoa\ScenarioStateBehatExtension\StoreInterface
     */
    private function getStore() 
    {
        return $this->scenarioStateInitializer
            ->getStore();
    }
}
