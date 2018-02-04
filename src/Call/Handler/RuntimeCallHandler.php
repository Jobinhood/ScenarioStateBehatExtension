<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Call\Handler;

use Behat\Behat\Transformation\Call\TransformationCall,
    Behat\Testwork\Environment\Call\EnvironmentCall,
    Behat\Behat\Definition\Call\DefinitionCall,
    Behat\Testwork\Call\Handler\CallHandler,
    Behat\Testwork\Hook\Call\HookCall,
    Behat\Testwork\Call\Call;

use Gorghoa\ScenarioStateBehatExtension\Resolver\AnnotationResolver;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class RuntimeCallHandler implements CallHandler
{
    /**
     * @var CallHandler
     */
    private $decorated;

    /**
     * @var AnnotationResolver
     */
    private $annotationResolver;

    /**
     * @param CallHandler $decorated
     * @param AnnotationResolver $annotationResolver
     */
    public function __construct(CallHandler $decorated, AnnotationResolver $annotationResolver)
    {
        $this->decorated = $decorated;
        $this->annotationResolver = $annotationResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCall(Call $call)
    {
        return $this->decorated->supportsCall($call);
    }

    /**
     * {@inheritdoc}
     */
    public function handleCall(Call $call)
    {
        /** @var \ReflectionMethod $function */
        $function = $call->getCallee()->getReflection();
        $arguments = $call->getArguments();

        if ($call instanceof HookCall) {
            $scope = $call->getScope();

            // Manage `scope` argument
            foreach ($function->getParameters() as $parameter) {
                if (null !== $parameter->getClass() && get_class($scope) === $parameter->getClass()->getName()) {
                    $arguments[$parameter->getName()] = $scope;
                    break;
                }
            }
        }

        $arguments = $this->annotationResolver->resolve($function, $arguments);

        if ($call instanceof TransformationCall) {
            $call = new TransformationCall($call->getEnvironment(), $call->getDefinition(), $call->getCallee(), $arguments);
        } elseif ($call instanceof HookCall) {
            $call = new EnvironmentCall($call->getScope()->getEnvironment(), $call->getCallee(), $arguments);
        } elseif ($call instanceof DefinitionCall) {
            $call = new DefinitionCall($call->getEnvironment(), $call->getFeature(), $call->getStep(), $call->getCallee(), $arguments);
        }

        return $this->decorated->handleCall($call);
    }
}
