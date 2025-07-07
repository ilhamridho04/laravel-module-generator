<?php

namespace NgodingSkuyy\LaravelModuleGenerator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use NgodingSkuyy\LaravelModuleGenerator\Commands\MakeFeature;
use Illuminate\Filesystem\Filesystem;

class MakeFeatureCommandUnitTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_make_feature_command()
    {
        $command = new MakeFeature();
        $this->assertInstanceOf(MakeFeature::class, $command);
    }

    /** @test */
    public function it_has_correct_command_signature()
    {
        $command = new MakeFeature();

        // Use reflection to access protected signature property
        $reflection = new \ReflectionClass($command);
        $signatureProperty = $reflection->getProperty('signature');
        $signatureProperty->setAccessible(true);
        $signature = $signatureProperty->getValue($command);

        $this->assertStringContainsString('module:create', $signature);
        $this->assertStringContainsString('{name?}', $signature);
        $this->assertStringContainsString('--with=*', $signature);
        $this->assertStringContainsString('--force', $signature);
    }

    /** @test */
    public function it_has_correct_description()
    {
        $command = new MakeFeature();

        $description = $command->getDescription();
        $this->assertStringContainsString('Generate full CRUD feature', $description);
    }

    /** @test */
    public function it_has_filesystem_dependency()
    {
        $command = new MakeFeature();

        // Check that the command has files property
        $reflection = new \ReflectionClass($command);
        $property = $reflection->getProperty('files');
        $property->setAccessible(true);
        $files = $property->getValue($command);

        $this->assertInstanceOf(Filesystem::class, $files);
    }
}
