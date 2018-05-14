<?php
use Knp\Provider\ConsoleServiceProvider;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../vendor/gboudreau/nest-api/nest.class.php';

$app = new Silex\Application();

$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/config/config.yml'));

$app->register(new ConsoleServiceProvider(), array(
    'console.name' => 'NestDashboard',
    'console.version' => '1.1.0',
    'console.project_directory' => __DIR__.'/..',
));

$console = $app['console'];
$console->add(new \Command\FetchCommand());
$console->run();
