<?php
require_once('./dbconnection.php');

// Feed::$cacheDir = __DIR__ . '/tmp';
// Feed::$cacheExpire = '5 hours';
// Feed::$userAgent = "BINUSTodayBot/1.0 (+https://binustoday.reinhart1010.id)";

$enable_youtube = false;
$youtube_only = false;

global $argv;
foreach ($argv as $arg){
    switch($arg){
        case '--youtube-only':
            $enable_youtube = true;
            $youtube_only = true;
            break;
        case '--enable-youtube':
            $enable_youtube = true;
            break;
    }
}

// Extract RSS feeds
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

        $is_youtube = str_starts_with($url, 'https://www.youtube.com/');

        if ($is_youtube && !$enable_youtube){
            print('Skipping YouTube RSS parsing to prevent detection of automated queries. See https://support.google.com/websearch/answer/86640.' . PHP_EOL . PHP_EOL);
            continue;
        } else if (!$is_youtube && $youtube_only){
            print('Skipping parsing as user only wants to parse YouTube feeds' . PHP_EOL . PHP_EOL);
            continue;
        }

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

            if (strpos($item['id'], '/gallery/') !== false) $item['type'] = 'GALLERY';
            if (isset($entry->category) && strtolower($entry->category->attributes()->term) == 'news') $item['type'] = 'NEWS';
            if (isset($item['content']) && strpos($item['content'], 'src="https://open.spotify.com/embed/') !== false) $item['type'] = 'PODCAST';

            if (isset($entry->pubDate)) $item['timestamp'] = (int) strtotime($entry->pubDate . " UTC");
            else if (isset($entry->published)) $item['timestamp'] = (int) strtotime($entry->published . " UTC");
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
        }

        print("DONE" . PHP_EOL . PHP_EOL);
        sleep(5);
    }
}

// Extract WP-JSON feeds
$keys = array_keys($feeds_wp_json);

if (!$youtube_only) for ($i = 0; $i < count($keys); $i++){
    $key = $keys[$i];
    if (!is_array($feeds_wp_json[$key])){
        $url = $feeds_wp_json[$key];
        $feeds_wp_json[$key] = [];
        array_push($feeds_wp_json[$key], $url);
    }

    for ($j = 0; $j < count($feeds_wp_json[$key]); $j++){
        $url = $feeds_wp_json[$key][$j];
        print('Extracting ' . $url . PHP_EOL);

        try {
            $entries = json_decode(implode(file($url)), true);
        } catch (Exception $e){
            // Skip
            print("ERROR" . PHP_EOL);
            continue;
        }

        foreach ($entries as $entry){
            $item = [];

            if ($entry['status'] !== 'publish') continue;
            
            $item['id'] = $entry['link'];
            $item['title'] = $entry['title']['rendered'];
            $item['summary'] = $entry['excerpt']['rendered'];
            $item['content'] = $entry['content']['rendered'];

            if (strpos($item['id'], '/gallery/') !== false) $item['type'] = 'GALLERY';
            if (isset($item['content']) && strpos($item['content'], 'src="https://open.spotify.com/embed/') !== false) $item['type'] = 'PODCAST';

            $item['timestamp'] = (int) strtotime($entry['date_gmt'] . " UTC");
            $item['author'] = $key;

            if (isset($entry['_embedded']['wp:term'])) foreach ($entry['_embedded']['wp:term'] as $term){
                if (count($term) == 0) continue;
                $term = $term[0];
                
                if ($term['taxonomy'] == 'category') switch ($term['slug']){
                    case 'news':
                    case 'bnewshighlights': // BVoice Radio
                    case 'tech-news': // Filemagz
                    case 'event-news': // Filemagz
                        $item['type'] = 'NEWS';
                        break;
                }
            }
            if (isset($entry['featured_media']) && isset($entry['_embedded'])) $item['cover_image'] = $entry['_embedded']['wp:featuredmedia'][0]['source_url'];
            
            // print_r($item);
            print('| "' . $item['title'] . '" from ' . $item['author'] . PHP_EOL);

            if (db::table('articles')->where('id', $item['id'])->first()){
                db::table('articles')->where('id', $item['id'])->update($item);
            } else db::table('articles')->insert($item);
        }

        print("DONE" . PHP_EOL . PHP_EOL);
        sleep(5);
    }
}