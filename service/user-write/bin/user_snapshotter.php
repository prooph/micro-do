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

namespace Prooph\MicroDo\UserWrite\Script;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\EventStore;
use Prooph\Micro\SnapshotReadModel;
use Prooph\MicroDo\UserWrite\Infrastructure\UserAggregateDefinition;

$autoloader = require __DIR__ . '/../vendor/autoload.php';

$factories = include __DIR__ . '/../src/Infrastructure/factories.php';

$eventStore = $factories['eventStore']();
/* @var EventStore $eventStore */

$aggregateDefinition = new UserAggregateDefinition();

$readModel = new SnapshotReadModel(
    $factories['snapshotStore'](),
    new UserAggregateDefinition()
);

$projection = $eventStore->createReadModelProjection(
    'user_snapshots',
    $readModel
);

$projection
    ->fromStream($aggregateDefinition->streamName('')->toString())
    ->whenAny(function ($state, Message $event): void {
        $this->readModel()->stack('replay', $event);
    })
    ->run();