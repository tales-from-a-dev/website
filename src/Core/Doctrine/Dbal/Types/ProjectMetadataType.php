<?php

declare(strict_types=1);

namespace App\Core\Doctrine\Dbal\Types;

use App\Domain\Project\Model\MetadataInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
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

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        $rawValue = self::$serializer->normalize($value);

        return parent::convertToDatabaseValue($rawValue, $platform);
    }

    /**
     * @return \App\Domain\Project\Model\MetadataInterface<\App\Domain\Project\Model\GitHubProject>|null
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?MetadataInterface
    {
        $rawValue = parent::convertToPHPValue($value, $platform);

        if (null === $rawValue || [] === $rawValue) {
            return null;
        }

        try {
            return self::$serializer->denormalize($rawValue, MetadataInterface::class);
        } catch (\Exception $exception) {
            throw ConversionException::conversionFailed($value, $this->getName(), $exception);
        }
    }

    public function getName(): string
    {
        return Types::PROJECT_METADATA;
    }
}
