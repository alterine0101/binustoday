<?php
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

class db extends Illuminate\Database\Capsule\Manager{}

require('feedsources.php');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$capsule = new db;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'database' => $_ENV['DB_DATABASE'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
    'prefix' => '',
]);

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();
?>
<form method="POST">
  <div class="h-captcha" data-sitekey="<?= $_ENV['HCAPTCHA_SITEKEY'] ?>"></div>
  <script src="https://hcaptcha.com/1/api.js" async defer></script>
</form>