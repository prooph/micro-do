<?php

/**
 * This file is part of prooph/micro-do.
 * (c) 2016-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\MicroDo\UserWrite\Model\User;

use Prooph\Common\Messaging\Message;
use Prooph\MicroDo\UserWrite\Model\Command\RegisterUser;
use Prooph\MicroDo\UserWrite\Model\Event\UserWasRegistered;
use Prooph\MicroDo\UserWrite\Model\Exception\InvalidName;

function registerWithData(callable $stateResolver, RegisterUser $command): array
{
    assertName($command->name());

    return [UserWasRegistered::withData($command->userId(), $command->name(), $command->emailAddress(), 1)];
}

function assertName(string $name)
{
    if (empty($name)) {
        throw InvalidName::reason('Name must not be empty');
    }
}

function nextVersion(array $state): int
{
    $version = $state['version'] ?? 0;

    return ++$version;
}

function apply(array $state, Message ...$events): array
{
    foreach ($events as $event) {
        switch ($event->messageName()) {
            case UserWasRegistered::class:
                $state = \array_merge($state, $event->payload());
                break;
        }
    }

    return $state;
}
