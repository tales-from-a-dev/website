<?php

declare(strict_types=1);

namespace App\Infrastructure\Dbal\Types;

use App\Domain\ValueObject\GitHubProject;
use App\Domain\ValueObject\MetadataInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProjectMetadataType extends JsonType
{
    private static NormalizerInterface&DenormalizerInterface $serializer;

    public static function setSerializer(NormalizerInterface&DenormalizerInterface $serializer): void
    {
        self::$serializer = $serializer;
    }

    /**
     * @template T of MetadataInterface
     */
    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        $rawValue = self::$serializer->normalize($value);

        return parent::convertToDatabaseValue($rawValue, $platform);
    }

    /**
     * @return MetadataInterface<GitHubProject>|null
     */
    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?MetadataInterface
    {
        $rawValue = parent::convertToPHPValue($value, $platform);

        if (null === $rawValue || [] === $rawValue) {
            return null;
        }

        try {
            return self::$serializer->denormalize($rawValue, MetadataInterface::class);
        } catch (\Exception $exception) {
            throw ValueNotConvertible::new($value, $this->getName(), $exception->getMessage(), $exception);
        }
    }

    public function getName(): string
    {
        return Types::PROJECT_METADATA;
    }
}
