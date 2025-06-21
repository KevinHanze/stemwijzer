<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\DependencyInjection\Container;
use Psr\Container\NotFoundExceptionInterface;

// Dummy services for testing
class Logger {}
class Service {
    public function __construct(public Logger $logger) {}
}

$container = new Container();

// Basic
$container->set('std', fn() => new stdClass());
$std1 = $container->get('std');
assert($std1 instanceof stdClass);
echo "Basic service resolution\n";

// Singleton
$container->set('singleton', fn() => new stdClass(), true);
$s1 = $container->get('singleton');
$s2 = $container->get('singleton');
assert($s1 === $s2);
echo "Singleton instance sharing\n";


