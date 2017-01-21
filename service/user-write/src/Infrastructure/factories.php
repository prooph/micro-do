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

return [
    'eventStore' => function (): \Prooph\EventStore\EventStore {
        static $eventStore = null;
        if (null === $eventStore) {
            $connection = new PDO(getenv('PDO_DSN'), getenv('PDO_USER'), getenv('PDO_PWD'));
            $eventStore = new \Prooph\EventStore\Pdo\PostgresEventStore(
                new \Prooph\Common\Messaging\FQCNMessageFactory(),
                new \Prooph\Common\Messaging\NoOpMessageConverter(),
                $connection,
                new \Prooph\EventStore\Pdo\PersistenceStrategy\PostgresSimpleStreamStrategy()
            );
        }
        return $eventStore;
    },
    'producer' => function (): callable {
        return function (\Prooph\Common\Messaging\Message $message): void {
        };
    },
];