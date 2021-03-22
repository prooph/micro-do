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

namespace Prooph\MicroDo\UserWrite\Infrastructure\Dispatcher;

use Prooph\Common\Messaging\Message;
use Prooph\MicroDo\UserWrite\Infrastructure\UserAggregateDefinition;
use Prooph\MicroDo\UserWrite\Model\Command\RegisterUser;

$factories = include 'factories.php';

$commandMap = [
    RegisterUser::class => [
        'handler' => function (callable $stateResolver, Message $message): array {
            return \Prooph\MicroDo\UserWrite\Model\User\registerWithData($stateResolver, $message);
        },
        'definition' => UserAggregateDefinition::class,
    ],
];

return \Prooph\Micro\Kernel\buildCommandDispatcher(
    $factories['eventStore'](),
    $commandMap,
    $factories['snapshotStore']()
);
