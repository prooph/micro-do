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
/**
 * This file is part of the prooph/micro-do.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Prooph\MicroDo\Shared\Fn;

use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;
use Psr\Http\Message\RequestInterface;

function createMessageFromRequest(RequestInterface $request, array $messageNameMap = [], MessageFactory $messageFactory = null): Message
{
    $payload = requestPayload($request);

    if (! \array_key_exists('message_name', $payload)) {
        throw new \RuntimeException('Missing message name.', 400);
    }

    if (isset($messageNameMap[$payload['message_name']])) {
        $payload['message_name'] = $messageNameMap[$payload['message_name']];
    }

    if (null === $messageFactory) {
        $messageFactory = new FQCNMessageFactory();
    }

    return $messageFactory->createMessageFromArray($payload['message_name'], $payload);
}

function requestPayload(RequestInterface $request): array
{
    $contentType = \trim($request->getHeaderLine('Content-Type'));

    if (0 !== \strpos($contentType, 'application/json')) {
        throw new \RuntimeException('application/json', 406);
    }

    $payload = \json_decode((string) $request->getBody(), true);

    switch (\json_last_error()) {
        case JSON_ERROR_DEPTH:
            throw new \RuntimeException('Invalid JSON, maximum stack depth exceeded.', 400);
        case JSON_ERROR_UTF8:
            throw new \RuntimeException('Malformed UTF-8 characters, possibly incorrectly encoded.', 400);
        case JSON_ERROR_SYNTAX:
        case JSON_ERROR_CTRL_CHAR:
        case JSON_ERROR_STATE_MISMATCH:
            throw new \RuntimeException('Invalid JSON.', 400);
    }

    return null === $payload ? [] : $payload;
}
