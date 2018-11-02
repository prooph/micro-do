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

$factories = [];

$factories['pdoConnection'] = function (): PDO {
    static $connection;

    if (! $connection) {
        $connection = new PDO(\getenv('PDO_DSN'), \getenv('PDO_USER'), \getenv('PDO_PWD'));
    }

    return $connection;
};

$factories['mongoConnection'] = function (): \Prooph\MicroDo\Shared\MongoDb\MongoConnection {
    static $mongoConnection;

    if (! $mongoConnection) {
        $client = new \MongoDB\Client(\getenv('MONGO_SERVER'));
        $mongoConnection = new \Prooph\MicroDo\Shared\MongoDb\MongoConnection($client, \getenv('MONGO_DB_NAME'));
    }

    return $mongoConnection;
};

$factories['eventStore'] = function () use ($factories): \Prooph\EventStore\EventStore {
    static $eventStore = null;
    if (null === $eventStore) {
        $eventStore = new \Prooph\EventStore\Pdo\PostgresEventStore(
            new \Prooph\Common\Messaging\FQCNMessageFactory(),
            new \Prooph\Common\Messaging\NoOpMessageConverter(),
            $factories['pdoConnection'](),
            new \Prooph\EventStore\Pdo\PersistenceStrategy\PostgresSimpleStreamStrategy()
        );
    }

    return $eventStore;
};

$factories['snapshotStore'] = function () use ($factories): \Prooph\SnapshotStore\SnapshotStore {
    $mongoConnection = $factories['mongoConnection']();
    /** @var \Prooph\MicroDo\Shared\MongoDb\MongoConnection $mongoConnection */
    return new \Prooph\MongoDb\SnapshotStore\MongoDbSnapshotStore($mongoConnection->client(), $mongoConnection->dbName());
};

return $factories;
