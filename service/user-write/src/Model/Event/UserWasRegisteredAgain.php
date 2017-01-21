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

namespace Prooph\MicroDo\UserWrite\Model\Event;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;
use Prooph\MicroDo\UserWrite\Model\EmailAddress;
use Prooph\MicroDo\UserWrite\Model\UserId;

final class UserWasRegisteredAgain extends DomainEvent implements PayloadConstructable
{
    use PayloadTrait;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var EmailAddress
     */
    private $emailAddress;

    public static function withData(UserId $userId, string $name, EmailAddress $emailAddress): UserWasRegisteredAgain
    {
        $event = new self([
            'user_id' => $userId->toString(),
            'name' => $name,
            'email' => $emailAddress->toString(),
        ]);

        $event->userId = $userId;
        $event->username = $name;
        $event->emailAddress = $emailAddress;

        return $event;
    }

    public function userId(): UserId
    {
        if (null === $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function name(): string
    {
        if (null === $this->username) {
            $this->username = $this->payload['name'];
        }

        return $this->username;
    }

    public function emailAddress(): EmailAddress
    {
        if (null === $this->emailAddress) {
            $this->emailAddress = EmailAddress::fromString($this->payload['email']);
        }

        return $this->emailAddress;
    }
}
