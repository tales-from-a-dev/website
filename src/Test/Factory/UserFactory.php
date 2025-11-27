<?php

declare(strict_types=1);

namespace App\Test\Factory;

use App\Domain\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->text(255),
            'password' => '$2y$13$wwgbz4O8Sl1cx1NoUYA3aOKwPGboT9nh.qpnNKclec64.QlKlDRXO',
            'roles' => [],
        ];
    }
}
