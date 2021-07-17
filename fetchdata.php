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

// Feed::$cacheDir = __DIR__ . '/tmp';
// Feed::$cacheExpire = '5 hours';
// Feed::$userAgent = "BINUSTodayBot/1.0 (+https://binustoday.reinhart1010.id)";

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();

$keys = array_keys($feeds);

for ($i = 0; $i < count($keys); $i++){
    $key = $keys[$i];
    if (!is_array($feeds[$key])){
        $url = $feeds[$key];
        $feeds[$key] = [];
        array_push($feeds[$key], $url);
    }

    for ($j = 0; $j < count($feeds[$key]); $j++){
        $url = $feeds[$key][$j];
        print('Extracting ' . $url . PHP_EOL);

        $use_atom = !(strpos($url, '/atom') == false);

        try {
            if ($use_atom) $feed = Feed::loadAtom($url);
            else $feed = Feed::loadRss($url);
        } catch (Exception $e){
            // Skip
            print("ERROR" . PHP_EOL);
            continue;
        }

        if ($use_atom) $entries = $feed->entry;
        else $entries = $feed->items;

        foreach ($entries as $entry){
            $item = [];
            
            try {
                $item['id'] = (string) $entry->link->attributes()->{'href'};
            } catch (Exception $e){
                $item['id'] = (string) $entry->link;
            }
            $item['title'] = (string) $entry->title;
            $item['summary'] = (string) $entry->summary;
            if (isset($entry->content)) $item['content'] = (string) $entry->content;
            else if (isset($entry->{'content:encoded'})) $item['content'] = (string) $entry->{'content:encoded'};
            $item['timestamp'] = (int) $entry->timestamp;
            $item['author'] = $key;
            
            if (isset($entry->enclosure)) $item['cover_image'] = (string) $entry->enclosure->attributes()->{'url'};
            else if (isset($entry->{'media:group'})) $item['cover_image'] = (string) $entry->{'media:group'}->{'media:thumbnail'}->attributes()->{'url'};

            // print_r($item);
            print('| "' . $item['title'] . '" from ' . $item['author'] . PHP_EOL);

            if (db::table('articles')->where('id', $item['id'])->first()){
                db::table('articles')->where('id', $item['id'])->update($item);
            } else db::table('articles')->insert($item);
            
        }

        print("DONE" . PHP_EOL . PHP_EOL);
    }
}
