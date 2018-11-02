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

namespace Prooph\MicroDo\UserWrite\Script;

use Prooph\Common\Messaging\Message;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\Projection\PostgresProjectionManager;
use Prooph\Micro\SnapshotReadModel;
use Prooph\MicroDo\UserWrite\Infrastructure\UserAggregateDefinition;
use Prooph\MongoDb\SnapshotStore\MongoDbSnapshotStore;

$autoloader = require __DIR__ . '/../vendor/autoload.php';

$factories = include __DIR__ . '/../src/Infrastructure/factories.php';

$eventStore = $factories['eventStore']();
$pdoConnection = $factories['pdoConnection']();
$aggregateDefinition = new UserAggregateDefinition();
$mongoClient = $factories['mongoConnection']()->client();

/* @var EventStore $eventStore */

$readModel = new SnapshotReadModel(
    $factories['snapshotStore'](),
    new UserAggregateDefinition()
);

$projectionManager = new PostgresProjectionManager($eventStore, $pdoConnection);

$snapshotReadModel = new SnapshotReadModel(
    new MongoDbSnapshotStore($mongoClient, 'user_snapshots'),
    $aggregateDefinition
);

$projection = $projectionManager->createReadModelProjection('user_snapshots', $snapshotReadModel);

$projection->fromStream($aggregateDefinition->streamName()->toString())
    ->whenAny(function ($state, Message $event): void {
        $this->readModel()->stack('replay', $event);
    })
    ->run();
