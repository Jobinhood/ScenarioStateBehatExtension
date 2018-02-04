<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\Argument;

use Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer,
    Gorghoa\ScenarioStateBehatExtension\Annotation\ScenarioStateAutoload;

use Behat\Testwork\Argument\ArgumentOrganiser;
use Doctrine\Common\Annotations\Reader;
use ReflectionFunctionAbstract;

/**
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ScenarioStateArgumentOrganiser implements ArgumentOrganiser
{
    /**
     * @var ArgumentOrganiser
     */
    private $baseOrganiser;

    /**
     * @var ScenarioStateInitializer
     */
    private $scenarioStateInitializer;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct(
        ArgumentOrganiser $organiser, 
        ScenarioStateInitializer $scenarioStateInitializer, 
        Reader $reader)
    {
        $this->baseOrganiser = $organiser;
        $this->scenarioStateInitializer = $scenarioStateInitializer;
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function organiseArguments(ReflectionFunctionAbstract $function, array $arguments)
    {
        // Todo: Not sure why the organiser is needed as it is called prior to
        // the AnnotationReader

        return $this->baseOrganiser->organiseArguments($function, $arguments);
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
