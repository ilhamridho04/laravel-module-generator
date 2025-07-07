<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;
use ReflectionClass;

class MakeFeatureInteractiveMenuTest extends TestCase
{
    /** @test */
    public function it_has_show_generation_mode_menu_method()
    {
        $command = new MakeFeature();
        $reflection = new ReflectionClass($command);

        $this->assertTrue($reflection->hasMethod('showGenerationModeMenu'));

        $method = $reflection->getMethod('showGenerationModeMenu');
        $this->assertTrue($method->isProtected());
        $this->assertEquals('string', $method->getReturnType()->getName());
    }

    /** @test */
    public function it_has_interactive_logic_in_handle_method()
    {
        $reflection = new ReflectionClass(MakeFeature::class);
        $handleMethodSource = file_get_contents($reflection->getFileName());

        // Check if interactive menu logic exists
        $this->assertStringContainsString('showGenerationModeMenu', $handleMethodSource);
        $this->assertStringContainsString('If no mode is specified via options, show interactive menu', $handleMethodSource);
        $this->assertStringContainsString('$generationMode = $this->showGenerationModeMenu()', $handleMethodSource);
    }

    /** @test */
    public function it_can_instantiate_make_feature_command_with_interactive_support()
    {
        $command = new MakeFeature();

        $this->assertInstanceOf(MakeFeature::class, $command);
        $this->assertEquals('module:create', $command->getName());
        $this->assertTrue($command->getDefinition()->hasOption('api'));
        $this->assertTrue($command->getDefinition()->hasOption('view'));
    }

    /** @test */
    public function it_has_determine_generation_mode_method()
    {
        $command = new MakeFeature();
        $reflection = new ReflectionClass($command);
        $method = $reflection->getMethod('determineGenerationMode');
        $method->setAccessible(true);

        // Test all modes
        $this->assertEquals('API Only', $method->invoke($command, true, false));
        $this->assertEquals('View Only', $method->invoke($command, false, true));
        $this->assertEquals('Full-stack (API + View)', $method->invoke($command, false, false));
    }
}
