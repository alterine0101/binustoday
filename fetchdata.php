<?php
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

class db extends Illuminate\Database\Capsule\Manager{}

$feeds = [
    // 'BINUS Group' => 'https://www.binus.edu/feed/atom/',
    'BINUS TV' => 'http://binus.tv/feed/atom/',
    'BINUS TV Club' => 'https://student-activity.binus.ac.id/binustvclub/feed/atom/',
    'BINUS University' => 'https://binus.ac.id/feed/atom/',
    'BNCC' => 'https://student-activity.binus.ac.id/bncc/feed/atom/',
    'BNEC' => [
        'https://student-activity.binus.ac.id/bnec/feed/atom/',
    ],
    'BVoice Radio' => [
        'https://www.bvoiceradio.com/feed/atom/',
        'https://student-activity.binus.ac.id/bvoice/feed/atom/'
    ],
    'Filemagz' => 'https://www.filemagz.com/feed/atom/',
    'HIMSISFO' => 'https://student-activity.binus.ac.id/himsisfo/feed/atom/',
    'HIMTI' => [
        'https://student-activity.binus.ac.id/himti/feed/atom/',
    ],
    'Nippon Club' => [
        'https://nipponclub.net/rss/',
        'https://student-activity.binus.ac.id/nc/feed/atom/'
    ],
    'SACD BINUS (@studentbinus)' => 'https://student.binus.ac.id/feed/atom/',
    'SCAC BINUS' => 'https://student-activity.binus.ac.id/feed/atom/',
    'School of Computer Science' => 'https://socs.binus.ac.id/feed/atom/',
    'School of Information Systems' => 'https://sis.binus.ac.id/feed/atom/',
    'Teach for Indonesia' => 'http://www.teachforindonesia.org/feed/atom/',
    'TFI Student Committee' => 'https://student-activity.binus.ac.id/tfi/feed/atom/',
];

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
Feed::$userAgent = "FeedFetcher-Google; (+http://www.google.com/feedfetcher.html)";

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();

$keys = array_keys($feeds);

$collected_items = [];

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

        if ($use_atom) $feed = Feed::loadAtom($url);
        else $feed = Feed::loadRss($url);

        $item = [];

        if ($use_atom) $entries = $feed->entry;
        else $entries = $feed->items;

        foreach ($entries as $entry){
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

            print_r($item);
            print('Got "' . $item['title'] . '" from ' . $item['author'] . PHP_EOL);

            array_push($collected_items, $item);
            sleep(10);
        }
    }
}

foreach ($collected_items as $item){
    if (db::table('articles')->where('id', $item['id'])->first()){
        db::table('articles')->where('id', $item['id'])->update($item);
    } else db::table('articles')->insert($item);
}
