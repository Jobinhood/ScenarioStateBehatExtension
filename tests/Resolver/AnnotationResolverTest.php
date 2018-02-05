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

use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer,
    Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateAutoload,
    Gorghoa\ScenarioStateBehatExtension\StoreInterface,
    Gorghoa\ScenarioStateBehatExtension\TestApp\Banana,
    Gorghoa\ScenarioStateBehatExtension\TestApp\Bonobo;

use Doctrine\Common\Annotations\Reader;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class AnnotationResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Background:
     * ==========
     *
     * Passing the following annotations:
     *
     *     @ScenarioStateAutoload(banana)
     *     @ScenarioStateAutoload(bonobo)
     *
     * to a function with the following arguments:
     *
     *    public function testBehatFunction(Bonobo $bonobo, Banana $banana)
     *
     * will trigger Resolver::resolve() to autoload $bonobo and $banana from the store.
     *
     * Test:
     * =====
     *
     * Resolver::resolve($function, $arguments) with the following arguments:
     *
     * array(
     *     'bonobo' => '@bonobo',
     *     'banana' => '@banana'
     * )
     *
     * should resolve to an array with the autoloaded items from the store:
     *
     * array(
     *     'bonobo' => \Bonobo,
     *     'banana' => \Banana
     * )
     *
     */
    public function testResolve()
    {
        // Mock Store
        $store = $this->prophesize(StoreInterface::class);

        $store->has('@banana')
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        $store->get('@banana')
            ->shouldBeCalledTimes(1)
            ->willReturn(new Banana());

        $store->get('@bonobo')
            ->shouldBeCalledTimes(1)
            ->willReturn(new Bonobo());
        
        $store->has('@bonobo')
            ->shouldBeCalledTimes(1)
            ->willReturn(true);

        // Mock Initializer
        $initializer = $this->prophesize(ScenarioStateInitializer::class);

        $initializer->getStore()
            ->willReturn($store)
            ->shouldBeCalledTimes(4);

        // Mock Annotation
        $annotation = $this->prophesize(ScenarioStateAutoload::class);

        $annotation->getArgument()
            ->willReturn('bonobo', 'banana')
            ->shouldBeCalledTimes(2);

        // Mock function & parameter
        $parameter = $this->prophesize(\ReflectionParameter::class);
        $function = $this->prophesize(\ReflectionMethod::class);

        $function->getParameters()
            ->willReturn([$parameter, $parameter])
            ->shouldBeCalledTimes(1);

        $parameter->getName()
            ->willReturn('bonobo','banana')
            ->shouldBeCalledTimes(2);


        // Mock Reader
        $reader = $this->prophesize(Reader::class);

        $reader->getMethodAnnotation($function, ScenarioStateAutoload::class)
            ->willReturn($annotation)
            ->shouldBeCalledTimes(1);

        $reader->getMethodAnnotations($function)
            ->willReturn([$this->prophesize(\stdClass::class), $annotation, $annotation])
            ->shouldBeCalledTimes(1);

        // Mock Resolver
        $resolver = new AnnotationResolver($initializer->reveal(), $reader->reveal());

        // Test Resolve function
        $arguments = [
            'bonobo' => '@bonobo', 
            'banana' => '@banana'
        ];

        $arguments = $resolver->resolve($function->reveal(), $arguments);

        $this->assertEquals(2, sizeof($arguments));

        $this->assertArrayHasKey('bonobo', $arguments);
        $this->assertArrayHasKey('banana', $arguments);

        $this->assertInstanceOf(Bonobo::class, $arguments['bonobo']);
        $this->assertInstanceOf(Banana::class, $arguments['banana']);
    }
}
