<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Serializer\Encoder;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder as BaseJsonEncoder;

#[AutoconfigureTag('serializer.encoder')]
final class JsonLdEncoder implements EncoderInterface, DecoderInterface
{
    public const string FORMAT = 'jsonld';

    private BaseJsonEncoder $jsonEncoder;

    public function __construct()
    {
        $this->jsonEncoder = new BaseJsonEncoder(
            // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
            new JsonEncode(['json_encode_options' => \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_AMP | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE | \JSON_INVALID_UTF8_IGNORE]),
            new JsonDecode(['json_decode_associative' => true])
        );
    }

    public function decode(string $data, string $format, array $context = []): mixed
    {
        return $this->jsonEncoder->decode($data, $format, $context);
    }

    public function supportsDecoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        return $this->jsonEncoder->encode($data, $format, $context);
    }

    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
