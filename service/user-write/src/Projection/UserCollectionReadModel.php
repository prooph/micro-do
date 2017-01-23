<?php
/**
 * This file is part of the prooph/micro-do.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Prooph\MicroDo\UserWrite\Projection;

final class UserCollectionReadModel implements \Prooph\EventStore\Projection\ReadModel
{
    const OP_INSERT_USER = 'INSERT_USER';

    /**
     * @var string
     */
    private $usersTable;

    /**
     * @var \PDO
     */
    private $connection;

    private $operations = [];

    public function __construct(\PDO $connection, string $usersTable)
    {
        $this->usersTable = $usersTable;
        $this->connection = $connection;
    }


    public function init(): void
    {
        throw new \BadMethodCallException('Initializing a user collection read model is not supported');
    }

    public function isInitialized(): bool
    {
        return true;
    }

    public function reset(): void
    {
        $query = <<<EOT
TRUNCATE TABLE {$this->usersTable}
    RESTART IDENTITY
    RESTRICT;
EOT;
        $statement = $this->connection->prepare($query);
        $statement->execute();

    }

    public function delete(): void
    {
        throw new \BadMethodCallException('Deleting a user collection read model is not supported');
    }

    public function stack(string $operation, ...$args): void
    {
        $this->operations[] = [
            $operation,
            $args
        ];
    }

    public function persist(): void
    {
        $this->connection->beginTransaction();

        foreach ($this->operations as list($operation, $args)) {
            switch ($operation) {
                case self::OP_INSERT_USER:
                    echo "todo";
                    break;
            }
        }
    }
}