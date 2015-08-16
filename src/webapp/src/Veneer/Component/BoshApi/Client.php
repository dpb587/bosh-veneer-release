<?php

namespace Veneer\Component\BoshApi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use Veneer\Component\BoshApi\Authentication\AuthenticationInterface;

class Client
{
    protected $guzzle;
    protected $authentication;

    public function __construct(AuthenticationInterface $authentication, array $clientOptions = [])
    {
        $stack = HandlerStack::create(isset($clientOptions['handler']) ? $clientOptions['handler'] : new CurlHandler());
        $stack->push(function (callable $handler) use ($authentication) {
            return function (RequestInterface $request, array $options) use ($handler, $authentication) {
                return $handler(
                    $request->withHeader('Authorization', $authentication->getAuthorizationHeader()),
                    $options
                );
            };
        });

        $clientOptions['handler'] = $stack;
        
        $this->guzzle = new GuzzleClient($clientOptions);

        $this->authentication = $authentication;
    }
}
