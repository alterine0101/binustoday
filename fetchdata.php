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

        try {
            $feed = simplexml_load_string(implode(file($url)));

            if (isset($feed->entry)){
                $entries = $feed->entry;
                $use_atom = true;
            } elseif (isset($feed->feed) && isset($feed->feed->entry)) {
                $entries = $feed->feed->entry;
                $use_atom = true;
            } else {
                $entries = $feed->channel->item;
                $use_atom = false;
            }
        } catch (Exception $e){
            // Skip
            print("ERROR" . PHP_EOL);
            continue;
        }

        foreach ($entries as $entry){
            $item = [];
            
            if ($use_atom) {
                $item['id'] = (string) $entry->link->attributes()->{'href'};
            } else {
                $item['id'] = (string) $entry->link;
            }
            $item['title'] = (string) $entry->title;

            if (isset($entry->description)) $item['summary'] = (string) $entry->description;
            else if (isset($entry->children('yt', TRUE)->videoId)){
                $item['type'] = 'VIDEO';
                $item['id'] = 'https://youtube.com/embed/' . ((string) $entry->children('yt', TRUE)->videoId);
                $item['summary'] = 'YouTube Video';
            } else $item['summary'] = (string) $entry->summary;

            if (isset($entry->content)) $item['content'] = (string) $entry->content;
            else if (isset($entry->children('content', TRUE)->encoded)) $item['content'] = (string) $entry->children('content', TRUE)->encoded;

            if (strpos($item['id'], '/gallery/')) $item['type'] = 'GALLERY';
            if (isset($entry->category) && strtolower($entry->category->attributes()->term) == 'news') $item['type'] = 'NEWS';
            if (isset($item['content']) && strpos($item['content'], 'src="https://open.spotify.com/embed/episode/') !== false) $item['type'] = 'PODCAST';

            if (isset($entry->pubDate)) $item['timestamp'] = (int) strtotime($entry->pubDate);
            else if (isset($entry->published)) $item['timestamp'] = (int) strtotime($entry->published);
            else $item['timestamp'] = (int) $entry->timestamp;
            $item['author'] = $key;
            
            if (isset($entry->enclosure)) $item['cover_image'] = (string) $entry->enclosure->attributes()->{'url'};
            else if (isset($entry->children('media', TRUE)->content)) $item['cover_image'] = (string) $entry->children('media', TRUE)->content->attributes()->url;
            else if (isset($entry->children('media', TRUE)->group)) $item['cover_image'] = (string) $entry->children('media', TRUE)->group->children('media', TRUE)->thumbnail->attributes()->url;

            // print_r($item);
            print('| "' . $item['title'] . '" from ' . $item['author'] . PHP_EOL);

            if (db::table('articles')->where('id', $item['id'])->first()){
                db::table('articles')->where('id', $item['id'])->update($item);
            } else db::table('articles')->insert($item);
            // sleep(10);
            
        }

        print("DONE" . PHP_EOL . PHP_EOL);
    }
}
