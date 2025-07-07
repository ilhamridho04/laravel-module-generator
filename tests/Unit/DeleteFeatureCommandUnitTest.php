<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use NgodingSkuyy\LaravelModuleGenerator\Commands\DeleteFeature;
use NgodingSkuyy\LaravelModuleGenerator\Tests\TestCase;

class DeleteFeatureCommandUnitTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_delete_feature_command()
    {
        $command = new DeleteFeature();

        $this->assertInstanceOf(DeleteFeature::class, $command);
    }

    /** @test */
    public function it_has_correct_command_signature()
    {
        $command = new DeleteFeature();

        // Use reflection to access protected signature property
        $reflection = new \ReflectionClass($command);
        $signatureProperty = $reflection->getProperty('signature');
        $signatureProperty->setAccessible(true);
        $signature = $signatureProperty->getValue($command);

        $this->assertStringContainsString('features:delete', $signature);
        $this->assertStringContainsString('{name}', $signature);
        $this->assertStringContainsString('--with=*', $signature);
        $this->assertStringContainsString('--all', $signature);
        $this->assertStringContainsString('--force', $signature);
    }

    /** @test */
    public function it_has_correct_description()
    {
        $command = new DeleteFeature();

        // Use reflection to access protected description property
        $reflection = new \ReflectionClass($command);
        $descriptionProperty = $reflection->getProperty('description');
        $descriptionProperty->setAccessible(true);
        $description = $descriptionProperty->getValue($command);

        $this->assertStringContainsString('Delete full CRUD feature', $description);
    }

    /** @test */
    public function it_has_filesystem_dependency()
    {
        $command = new DeleteFeature();

        // Use reflection to access protected files property
        $reflection = new \ReflectionClass($command);
        $filesProperty = $reflection->getProperty('files');
        $filesProperty->setAccessible(true);
        $files = $filesProperty->getValue($command);

        $this->assertInstanceOf(\Illuminate\Filesystem\Filesystem::class, $files);
    }
}
