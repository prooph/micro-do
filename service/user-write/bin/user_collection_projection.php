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

namespace Prooph\MicroDo\UserWrite\Script;

$autoloader = require __DIR__ . '/../vendor/autoload.php';

$factories = include __DIR__ . '/../src/Infrastructure/factories.php';

$eventStore = $factories['eventStore']();
/* @var EventStore $eventStore */


