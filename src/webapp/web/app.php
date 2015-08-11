<?php

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

require_once __DIR__.'/../app/AppKernel.php';

$symfonyEnvironment = getenv('SYMFONY_ENV') ?: 'dev';
$symfonyDebug = filter_var(getenv('SYMFONY_DEBUG'), FILTER_VALIDATE_BOOLEAN);

if ($symfonyDebug) {
    Symfony\Component\Debug\Debug::enable();
}

$kernel = new AppKernel($symfonyEnvironment, $symfonyDebug ?: false);
$kernel->loadClassCache();

$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
