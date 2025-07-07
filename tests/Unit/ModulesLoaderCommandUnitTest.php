<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Commands\SetupModulesLoader;
use NgodingSkuyy\LaravelModuleGenerator\Commands\InstallModulesLoader;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class ModulesLoaderCommandUnitTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_setup_modules_loader_command()
    {
        $command = new SetupModulesLoader();

        $this->assertInstanceOf(SetupModulesLoader::class, $command);
    }

    /** @test */
    public function setup_command_has_correct_signature()
    {
        $command = new SetupModulesLoader();

        // Use reflection to access protected signature property
        $reflection = new \ReflectionClass($command);
        $signatureProperty = $reflection->getProperty('signature');
        $signatureProperty->setAccessible(true);
        $signature = $signatureProperty->getValue($command);

        $this->assertStringContainsString('modules:setup', $signature);
        $this->assertStringContainsString('--force', $signature);
    }

    /** @test */
    public function setup_command_has_correct_description()
    {
        $command = new SetupModulesLoader();

        // Use reflection to access protected description property
        $reflection = new \ReflectionClass($command);
        $descriptionProperty = $reflection->getProperty('description');
        $descriptionProperty->setAccessible(true);
        $description = $descriptionProperty->getValue($command);

        $this->assertStringContainsString('Setup automatic module routes loader', $description);
    }

    /** @test */
    public function it_can_instantiate_install_modules_loader_command()
    {
        $command = new InstallModulesLoader();

        $this->assertInstanceOf(InstallModulesLoader::class, $command);
    }

    /** @test */
    public function install_command_has_correct_signature()
    {
        $command = new InstallModulesLoader();

        // Use reflection to access protected signature property
        $reflection = new \ReflectionClass($command);
        $signatureProperty = $reflection->getProperty('signature');
        $signatureProperty->setAccessible(true);
        $signature = $signatureProperty->getValue($command);

        $this->assertStringContainsString('modules:install', $signature);
        $this->assertStringContainsString('--force', $signature);
    }

    /** @test */
    public function install_command_has_correct_description()
    {
        $command = new InstallModulesLoader();

        // Use reflection to access protected description property
        $reflection = new \ReflectionClass($command);
        $descriptionProperty = $reflection->getProperty('description');
        $descriptionProperty->setAccessible(true);
        $description = $descriptionProperty->getValue($command);

        $this->assertStringContainsString('Install and integrate modules auto-loader', $description);
    }

    /** @test */
    public function both_commands_have_filesystem_dependency()
    {
        $setupCommand = new SetupModulesLoader();
        $installCommand = new InstallModulesLoader();

        // Use reflection to access protected files property
        $reflection = new \ReflectionClass($setupCommand);
        $filesProperty = $reflection->getProperty('files');
        $filesProperty->setAccessible(true);
        $files = $filesProperty->getValue($setupCommand);

        $this->assertInstanceOf(\Illuminate\Filesystem\Filesystem::class, $files);

        $reflection = new \ReflectionClass($installCommand);
        $filesProperty = $reflection->getProperty('files');
        $filesProperty->setAccessible(true);
        $files = $filesProperty->getValue($installCommand);

        $this->assertInstanceOf(\Illuminate\Filesystem\Filesystem::class, $files);
    }
}
