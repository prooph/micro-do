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

namespace Prooph\MicroDo\UserWrite\Model\Exception;

use Prooph\MicroDo\UserWrite\Model\UserId;

final class UserAlreadyExists extends \InvalidArgumentException
{
    public static function withUserId(UserId $userId): UserAlreadyExists
    {
        return new self(\sprintf('User with id %s already exists.', $userId->toString()));
    }
}
