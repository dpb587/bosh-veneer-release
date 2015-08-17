<?php

namespace Veneer\Component\BoshApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use Veneer\Component\BoshApi\Authentication\AuthenticationInterface;
use GuzzleHttp\Psr7\Request;

class Client extends GuzzleClient
{
    public function __construct(array $clientOptions, AuthenticationInterface $authentication)
    {
        $stack = HandlerStack::create(isset($clientOptions['handler']) ? $clientOptions['handler'] : new CurlHandler());
        $stack->push(function (callable $handler) use ($authentication) {
            return function (Request $request, array $options) use ($handler, $authentication) {
                return $handler(
                    $request->withHeader('Authorization', $authentication->getAuthorizationHeader()),
                    $options
                );
            };
        });

        $clientOptions['handler'] = $stack;
        
        parent::__construct($clientOptions);
    }

    public function getTaskOutput($taskId, $offset, $logType = 'result')
    {
        $response = $this->get(
            'tasks/' . $taskId . '/output',
            [
                'query' => [
                    'type' => $logType,
                ],
                #'headers' => [
                #    'Range' => 'bytes=' . $offset . '-',
                #,
            ]
        );

        $range = $response->getHeader('content-range');

        $result = [
            'data' => (string) $response->getBody(),
        ];

        if ((206 == $response->getStatusCode()) && isset($range[0]) && (preg_match('/bytes (\d+)-(\d+)\/\d+/', $range[0], $rangeMatch))) {
            $result['from'] = $rangeMatch[1];
            $result['to'] = $rangeMatch[2];
        }

        if ('event' == $logType) {
            $result['data'] = array_map(
                function ($v) {
                    return json_decode($v, true);
                },
                array_filter(
                    explode("\n", trim($result['data'])),
                    function ($v) {
                        return !empty($v);
                    }
                )
            );
        }

        return $result;
    }
}
