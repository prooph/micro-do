<?php
/**
 * This file is part of the prooph/micro-do.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
require '../vendor/autoload.php';

$messageMap = include '../src/Infrastructure/message_map.php';
$dispatcher = include '../src/Infrastructure/dispatcher.php';

$app = function(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response)
    use ($messageMap, $dispatcher) {
    try {
        $message = \Prooph\MicroDo\Shared\Fn\createMessageFromRequest($request, $messageMap);

        $result = $dispatcher($message);

        if($result instanceof \Throwable) {
            throw $result;
        }

        $noOpMessageConverter = new \Prooph\Common\Messaging\NoOpMessageConverter();

        return new \Zend\Diactoros\Response\JsonResponse([
            'events' => array_map(function(\Prooph\Common\Messaging\Message $message) use($noOpMessageConverter) {
                return $noOpMessageConverter->convertToArray($message);
            }, $result)
        ]);
    } catch (\Throwable $e) {
        error_log('[UserWriteService.Error] ' . $e);

        $code = 500;
        $message = 'Internal Server Error';

        if($e->getCode() >= 400 && $e->getCode() < 500) {
            $code = $e->getCode();
            $message = $e->getMessage();
        }

        return new \Zend\Diactoros\Response\JsonResponse([
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $code);
    }
};

$server = \Zend\Diactoros\Server::createServer(
    $app,
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$server->listen();