<?php

declare(strict_types=1);

namespace App\Core\Entity\Behavior;

use App\Core\Entity\Exception\SluggableException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

trait SluggableTrait
{
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $slug = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setSlug(): void
    {
        if (null !== $this->slug && false === $this->shouldRegenerateSlugOnUpdate()) {
            return;
        }

        $values = [];
        foreach ($this->getSluggableFields() as $sluggableField) {
            $values[] = $this->resolveFieldValue($sluggableField);
        }

        $this->slug = $this->generateSlug($values);
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    private function shouldRegenerateSlugOnUpdate(): bool
    {
        return true;
    }

    /**
     * @param array<int, mixed> $values
     */
    private function generateSlug(array $values): string
    {
        $usableValues = [];
        foreach ($values as $value) {
            if (!empty($value)) {
                $usableValues[] = $value;
            }
        }

        $this->ensureAtLeastOneUsableValue($values, $usableValues);

        // generate the slug itself
        $sluggableText = implode(' ', $usableValues);

        return (new AsciiSlugger())
            ->slug($sluggableText)
            ->lower()
            ->toString()
        ;
    }

    /**
     * @param array<int, mixed> $values
     * @param array<int, mixed> $usableValues
     *
     * @throws SluggableException
     */
    private function ensureAtLeastOneUsableValue(array $values, array $usableValues): void
    {
        if (\count($usableValues) >= 1) {
            return;
        }

        throw new SluggableException(sprintf('Sluggable expects to have at least one non-empty field from the following: ["%s"]', implode('", "', array_keys($values))));
    }

    private function resolveFieldValue(string $field): mixed
    {
        if (property_exists($this, $field)) {
            return $this->{$field};
        }

        $methodName = 'get'.ucfirst($field);
        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }

        return null;
    }
}
