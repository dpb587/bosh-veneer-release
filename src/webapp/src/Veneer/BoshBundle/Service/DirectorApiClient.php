<?php

namespace Veneer\BoshBundle\Service;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use Veneer\BoshBundle\Security\Core\Authentication\Token\AbstractToken;
use Veneer\BoshBundle\Security\Core\Authentication\Token\BasicToken;
use Veneer\BoshBundle\Security\Core\Authentication\Token\UaaToken;
use GuzzleHttp\Psr7\Request;

class DirectorApiClient extends GuzzleClient
{
    public function __construct(array $clientOptions, AbstractToken $token)
    {
        if ($token instanceof UaaToken) {
            $authorizationHeader = $token->getUser()->getCredentials();
        } elseif ($token instanceof BasicToken) {
            $authorizationHeader = 'Basic ' . base64_encode($token->getUsername() . ':' . ($token->getCredentials() ?: $token->getUser()->getCredentials()));
        } else {
            throw new \InvalidArgumentException('Token must be Uaa or Basic');
        }

        $stack = HandlerStack::create(isset($clientOptions['handler']) ? $clientOptions['handler'] : new CurlHandler());
        $stack->push(function (callable $handler) use ($authorizationHeader) {
            return function (Request $request, array $options) use ($handler, $authorizationHeader) {
                return $handler(
                    $request->withHeader('Authorization', $authorizationHeader),
                    $options
                );
            };
        });

        $clientOptions['handler'] = $stack;
        
        parent::__construct($clientOptions);
    }

    public function getInfo()
    {
        return json_decode($this->get('info')->getBody(), true);
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
