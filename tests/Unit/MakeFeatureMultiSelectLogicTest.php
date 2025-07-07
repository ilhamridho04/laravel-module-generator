<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class MakeFeatureMultiSelectLogicTest extends TestCase
{
    /** @test */
    public function it_has_multi_select_method_signature()
    {
        // Test that the askOptionalComponents method exists and can be called
        $makeFeatureClass = new \ReflectionClass(\NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature::class);
        $method = $makeFeatureClass->getMethod('askOptionalComponents');

        $this->assertTrue($method->isProtected());
        $this->assertEquals('array', $method->getReturnType()->getName());
    }

    /** @test */
    public function it_has_correct_available_components()
    {
        // Test that all expected components are available in the multi-select
        // We can verify this by checking the stub files exist
        $stubsPath = __DIR__ . '/../../src/stubs/';

        // These stubs should exist for the optional components
        $this->assertFileExists($stubsPath . 'Enum.stub');
        $this->assertFileExists($stubsPath . 'Observer.stub');
        // Policy and Factory use Laravel's built-in commands
        // Test stub is created by MakeFeature itself
    }

    /** @test */
    public function command_signature_has_optional_name_argument()
    {
        $command = new \NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature();
        $definition = $command->getDefinition();

        $nameArgument = $definition->getArgument('name');
        $this->assertFalse($nameArgument->isRequired(), 'Name argument should be optional for interactive mode');
    }

    /** @test */
    public function command_has_with_option_for_components()
    {
        $command = new \NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('with'));

        $withOption = $definition->getOption('with');
        $this->assertTrue($withOption->isArray(), 'with option should accept multiple values');
    }

    /** @test */
    public function command_has_api_and_view_options()
    {
        $command = new \NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature();
        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('api'));
        $this->assertTrue($definition->hasOption('view'));
        $this->assertTrue($definition->hasOption('force'));
        $this->assertTrue($definition->hasOption('skip-install'));
    }
}
