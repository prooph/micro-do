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

namespace Prooph\MicroDo\UserWrite\Infrastructure;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\StreamName;
use Prooph\Micro\AbstractAggregateDefiniton;

final class UserAggregateDefinition extends AbstractAggregateDefiniton
{
    public function identifierName(): string
    {
        return 'user_id';
    }

    public function streamName(string $aggregateId): StreamName
    {
        return new StreamName('user_stream');
    }

    public function apply(array $state, Message ...$events): array
    {
        return \Prooph\MicroDo\UserWrite\Model\User\apply($state, ...$events);
    }

    public function aggregateType(): string
    {
        return 'user';
    }
}
