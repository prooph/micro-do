<?php
/**
 * This file is part of the prooph/micro-do.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\MicroDo\UserWrite\Model\User;

use Prooph\Common\Messaging\Message;
use Prooph\Micro\AggregateResult;
use Prooph\MicroDo\UserWrite\Model\Command\RegisterUser;
use Prooph\MicroDo\UserWrite\Model\Event\UserWasRegistered;
use Prooph\MicroDo\UserWrite\Model\Exception\InvalidName;

function registerWithData(array $state, RegisterUser $command): AggregateResult {

    assertName($command->name());

    $event = UserWasRegistered::withData($command->userId(), $command->name(), $command->emailAddress());

    return new AggregateResult([$event], apply($state, $event));
}

function assertName(string $name)
{
    if(empty($name)) {
        throw InvalidName::reason('Name must not be empty');
    }
}

function apply(array $state, Message ...$events): array
{
    foreach ($events as $event) {
        switch ($event->messageName()) {
            case UserWasRegistered::class:
                return array_merge($state, $event->payload());
        }
    }

    return $state;
}