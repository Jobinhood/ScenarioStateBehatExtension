<?php

/*
 * This file is part of the ScenarioStateBehatExtension project.
 *
 * (c) Rodrigue Villetard <rodrigue.villetard@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gorghoa\ScenarioStateBehatExtension\ServiceContainer;

use Gorghoa\ScenarioStateBehatExtension\Hook\Tester\ScenarioStateHookableScenarioTester,
    Gorghoa\ScenarioStateBehatExtension\Context\Initializer\ScenarioStateInitializer,
    Gorghoa\ScenarioStateBehatExtension\Hook\Dispatcher\ScenarioStateHookDispatcher,
    Gorghoa\ScenarioStateBehatExtension\Argument\ScenarioStateArgumentOrganiser,
    Gorghoa\ScenarioStateBehatExtension\Call\Handler\RuntimeCallHandler,
    Gorghoa\ScenarioStateBehatExtension\Resolver\AnnotationResolver;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension,
    Behat\Testwork\ServiceContainer\Extension as ExtensionInterface,
    Behat\Testwork\Argument\ServiceContainer\ArgumentExtension,
    Behat\Behat\Context\ServiceContainer\ContextExtension,
    Behat\Testwork\Call\ServiceContainer\CallExtension,
    Behat\Testwork\ServiceContainer\ExtensionManager;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Reference;

use Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader;

/**
 * Behat store for Behat contexts.
 *
 * @author Rodrigue Villetard <rodrigue.villetard@gmail.com>
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class ScenarioStateExtension implements ExtensionInterface
{
    const SCENARIO_STATE_ANNOTATION_RESOLVER_ID = 'scenario_state.arguments_resolver';
    const SCENARIO_STATE_ARGUMENT_ORGANISER_ID = 'argument.scenario_state.organiser';
    const SCENARIO_STATE_CALL_HANDLER_ID = 'call.scenario_state.call_handler';
    const SCENARIO_STATE_DISPATCHER_ID = 'hook.scenario_state.dispatcher';
    const SCENARIO_STATE_INITIALIZER_ID = 'behatstore.context_initializer.scenario_state';
    const SCENARIO_STATE_READER_ID = 'doctrine.reader.annotation';
    const SCENARIO_STATE_TESTER_ID = 'tester.scenario_state.wrapper';

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'scenario_state';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        AnnotationRegistry::registerFile(__DIR__.'/../Annotation/ScenarioStateAutoload.php');
        AnnotationRegistry::registerFile(__DIR__.'/../Annotation/ScenarioStateAutosave.php');
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        // Load ScenarioStateInitializer
        $container->register(self::SCENARIO_STATE_INITIALIZER_ID, ScenarioStateInitializer::class)
            ->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0])
            ->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);

        // Declare Doctrine annotation reader as service
        $readerDefinition = $container->register(
            self::SCENARIO_STATE_READER_ID, 
            AnnotationReader::class
        );
        // Ignore Behat annotations in reader
        $keywords = [
            'Given', 'When', 'Then',
            'Transform',
            'BeforeStep', 'AfterStep',
            'BeforeScenario', 'AfterScenario',
            'BeforeSuite', 'AfterSuite',
            'BeforeFeature', 'AfterFeature',
        ];
        foreach ($keywords as $keyword) {
            $readerDefinition->addMethodCall('addGlobalIgnoredName', [$keyword]);
            $readerDefinition->addMethodCall('addGlobalIgnoredName', [strtolower($keyword)]);
        }

        // Arguments resolver: resolve ScenarioState arguments from annotation
        $container->register(self::SCENARIO_STATE_ANNOTATION_RESOLVER_ID, AnnotationResolver::class)
            ->setArguments([
                new Reference(self::SCENARIO_STATE_INITIALIZER_ID),
                new Reference(self::SCENARIO_STATE_READER_ID),
            ]);

        // Argument organiser
        $container->register(self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID, ScenarioStateArgumentOrganiser::class)
            ->setDecoratedService(ArgumentExtension::PREG_MATCH_ARGUMENT_ORGANISER_ID)
            ->setPublic(false)
            ->setArguments([
                new Reference(sprintf('%s.inner', self::SCENARIO_STATE_ARGUMENT_ORGANISER_ID)),
                new Reference(self::SCENARIO_STATE_INITIALIZER_ID),
                new Reference(self::SCENARIO_STATE_READER_ID),
                new Reference(self::SCENARIO_STATE_ANNOTATION_RESOLVER_ID),
            ]);

        // Override calls process
        $container->register(self::SCENARIO_STATE_CALL_HANDLER_ID, RuntimeCallHandler::class)
            ->setDecoratedService(CallExtension::CALL_HANDLER_TAG.'.runtime')
            ->setArguments([
                new Reference(self::SCENARIO_STATE_CALL_HANDLER_ID.'.inner'),
                new Reference(self::SCENARIO_STATE_ANNOTATION_RESOLVER_ID),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
