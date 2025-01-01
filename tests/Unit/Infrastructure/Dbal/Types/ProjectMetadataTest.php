<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Dbal\Types;

use App\Domain\ValueObject\GitHubProject;
use App\Infrastructure\Dbal\Types\ProjectMetadataType;
use App\Infrastructure\Dbal\Types\Types;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProjectMetadataTest extends KernelTestCase
{
    protected AbstractPlatform $platform;
    protected ProjectMetadataType $type;

    #[\Override]
    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->type = new ProjectMetadataType();
    }

    public function testItReturnsBindingType(): void
    {
        self::assertSame(ParameterType::STRING, $this->type->getBindingType());
    }

    public function testItReturnsName(): void
    {
        self::assertSame(Types::PROJECT_METADATA, $this->type->getName());
    }

    public function testItConvertJsonNullToPhpValue(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testItConvertJsonEmptyStringToPhpValue(): void
    {
        self::assertNull($this->type->convertToPHPValue('', $this->platform));
    }

    public function testItConvertJsonEmptyObjectToPhpValue(): void
    {
        self::bootKernel();

        self::assertNull($this->type->convertToPHPValue('{}', $this->platform));
    }

    public function testItConvertJsonGithubObjectToPhpGithubProjectClass(): void
    {
        self::bootKernel();

        $value = ['type' => 'github_project', 'id' => '1', 'name' => 'foo', 'description' => 'foo bar', 'forkCount' => 10, 'stargazerCount' => 10, 'url' => 'https://github.com/foo/bar', 'languages' => [['name' => 'php', 'color' => '#000']]];
        $databaseValue = json_encode($value, \JSON_THROW_ON_ERROR | 0, \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION);

        $phpValue = $this->type->convertToPHPValue($databaseValue, $this->platform);

        self::assertInstanceOf(GitHubProject::class, $phpValue);
        self::assertSame('1', $phpValue->id);
        self::assertSame('foo', $phpValue->name);
        self::assertSame('foo bar', $phpValue->description);
        self::assertSame(10, $phpValue->forkCount);
        self::assertSame(10, $phpValue->stargazerCount);
        self::assertCount(1, $phpValue->languages);
        self::assertContains(['name' => 'php', 'color' => '#000'], $phpValue->languages);
    }

    public function testItConvertPhpNullValueToJsonNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testItConvertPhpGithubProjectClassToJsonGithubObject(): void
    {
        $source = new GitHubProject('1', 'foo', 'foo bar', 10, 10, 'https://github.com/foo/bar', [['name' => 'php', 'color' => '#000']]);
        $databaseValue = $this->type->convertToDatabaseValue($source, $this->platform);

        self::assertSame('{"type":"github_project","id":"1","name":"foo","description":"foo bar","forkCount":10,"stargazerCount":10,"url":"https:\/\/github.com\/foo\/bar","languages":[{"name":"php","color":"#000"}]}', $databaseValue);
    }

    public function testItThrowAnExceptionOnDenormalizationWithInvalidType(): void
    {
        $value = ['type' => 'metadata_project', 'id' => '1', 'name' => 'foo', 'description' => 'foo bar', 'forkCount' => 10, 'stargazerCount' => 10, 'languages' => [['name' => 'php', 'color' => '#000']]];
        $databaseValue = json_encode($value, 0, \JSON_THROW_ON_ERROR | \JSON_PRESERVE_ZERO_FRACTION);

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            'Could not convert database value to "project_metadata" as an error was triggered by the unserialization: The type "metadata_project" is not a valid value.',
        );
        $this->type->convertToPHPValue($databaseValue, $this->platform);
    }
}
