<?php

declare(strict_types=1);

namespace App\{{Module}}\Test\Factory;

use App\{{Module}}\Domain\Entity\{{Module}};
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<{{Module}}>
 */
final class {{Module}}Factory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return {{Module}}::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->words(3, true),
        ];
    }
}
