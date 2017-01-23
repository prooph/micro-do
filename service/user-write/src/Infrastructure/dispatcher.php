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

namespace Prooph\MicroDo\UserWrite\Infrastructure\Dispatcher;

use Prooph\Common\Messaging\Message;
use Prooph\Micro\AggregateResult;
use Prooph\MicroDo\UserWrite\Infrastructure\UserAggregateDefinition;
use Prooph\MicroDo\UserWrite\Model\Command\RegisterUser;

$factories = include 'factories.php';

$commandMap = [
    RegisterUser::class => [
        'handler' => function (array $state, Message $message): AggregateResult {
            return \Prooph\MicroDo\UserWrite\Model\User\registerWithData($state, $message);
        },
        'definition' => UserAggregateDefinition::class,
    ]
];

return \Prooph\Micro\Kernel\buildCommandDispatcher(
    $commandMap,
    $factories['eventStore'],
    $factories['producer'],
    $factories['snapshotStore']
);