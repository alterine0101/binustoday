<?php
require_once('./dbconnection.php');
require_once('./socialposter.php');

// Feed::$cacheDir = __DIR__ . '/tmp';
// Feed::$cacheExpire = '5 hours';
// Feed::$userAgent = "BINUSTodayBot/1.0 (+https://binustoday.reinhart1010.id)";

$enable_youtube = false;
$youtube_only = false;

global $argv;
foreach ($argv as $arg) {
    switch($arg) {
        case '--youtube-only':
            $enable_youtube = true;
            $youtube_only = true;
            break;
        case '--enable-youtube':
            $enable_youtube = true;
            break;
    }
}

$opts = [
    'http' => [
        'method' => "GET",
        'header' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Vivaldi/5.7.2921.53"
    ]
];

// Extract RSS feeds
$keys = array_keys($feeds);

// Shuffle to minimize breakage
shuffle($keys);

for ($i = 0; $i < count($keys); $i++) {
    $key = $keys[$i];
    if (!is_array($feeds[$key])) {
        $url = $feeds[$key];
        $feeds[$key] = [];
        array_push($feeds[$key], $url);
    }

    for ($j = 0; $j < count($feeds[$key]); $j++) {
        $url = $feeds[$key][$j];
        print('Extracting ' . $url . PHP_EOL);

        $is_youtube = str_starts_with($url, 'https://www.youtube.com/');

        if ($is_youtube && !$enable_youtube) {
            print('Skipping YouTube RSS parsing to prevent detection of automated queries. See https://support.google.com/websearch/answer/86640.' . PHP_EOL . PHP_EOL);
            continue;
        } else if (!$is_youtube && $youtube_only) {
            print('Skipping parsing as user only wants to parse YouTube feeds' . PHP_EOL . PHP_EOL);
            continue;
        }

        try {
            $context = stream_context_create($opts);
            if ($is_youtube) {
                $use_atom = true;
                $url = str_replace('https://www.youtube.com/feeds/videos.xml?channel_id=', $yt_alt[rand(0, count($yt_alt) - 1)] . 'feed/channel/', $url);
                print('Replacing URL to ' . $url . PHP_EOL);
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
            $raw_feed = curl_exec($curl);

            if (curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
                print('Skipping parsing as feed cannot be fetched');
                continue;
            };
            $feed = simplexml_load_string(trim($raw_feed));
            curl_close($curl);
            // $feed = simplexml_load_file($url);

            if (isset($feed->entry)) {
                $entries = $feed->entry;
                $use_atom = true;
            } elseif (isset($feed->feed) && isset($feed->feed->entry)) {
                $entries = $feed->feed->entry;
                $use_atom = true;
            } else {
                $entries = $feed->channel->item;
                $use_atom = false;
            }
        } catch (Exception $e) {
            // Skip
            print("ERROR" . PHP_EOL);
            continue;
        }

        foreach ($entries as $entry) {
            $item = [];

            // URL and title
            if ($use_atom) {
                $item['id'] = (string) $entry->link->attributes()->{'href'};
            } else {
                $item['id'] = (string) $entry->link;
            }
            $item['title'] = (string) $entry->title;

            // Description and YouTube detection
            if (isset($entry->children('yt', TRUE)->videoId)) {
                $item['type'] = 'VIDEO';
                $item['id'] = 'https://youtube.com/embed/' . ((string) $entry->children('yt', TRUE)->videoId);
                $item['summary'] = 'YouTube Video';
                $item['cover_image'] = 'https://i3.ytimg.com/vi/' . ((string) $entry->children('yt', TRUE)->videoId) . '/hqdefault.jpg';
            } else {
                // Description
                if (isset($entry->description)) $item['summary'] = (string) $entry->description;
                else $item['summary'] = (string) $entry->summary;

                // Cover Image
                if (isset($entry->enclosure)) $item['cover_image'] = (string) $entry->enclosure->attributes()->{'url'};
                else if (isset($entry->children('media', TRUE)->content)) $item['cover_image'] = (string) $entry->children('media', TRUE)->content->attributes()->url;
                else if (isset($entry->children('media', TRUE)->group)) $item['cover_image'] = (string) $entry->children('media', TRUE)->group->children('media', TRUE)->thumbnail->attributes()->url;

                // Content type detection
                if (strpos($item['id'], '/gallery/') !== false) $item['type'] = 'GALLERY';
                if (strpos($item['id'], '/video/') !== false) $item['type'] = 'VIDEO';
                if (strpos($item['id'], '/videos/') !== false) $item['type'] = 'VIDEO';
                if (isset($entry->category) && strtolower($entry->category->attributes()->term) == 'news') $item['type'] = 'NEWS';
                if (isset($item['content']) && (strpos($item['content'], 'src="https://open.spotify.com/embed/') !== false || strpos($item['content'], 'href="https://open.spotify.com/embed/') !== false)) $item['type'] = 'PODCAST';
            }

            // Item content
            if (isset($entry->content)) $item['content'] = (string) $entry->content;
            else if (isset($entry->children('content', TRUE)->encoded)) $item['content'] = (string) $entry->children('content', TRUE)->encoded;

            // Timestamp
            if (isset($entry->pubDate)) $item['timestamp'] = (int) strtotime($entry->pubDate . " UTC");
            else if (isset($entry->published)) $item['timestamp'] = (int) strtotime($entry->published . " UTC");
            else $item['timestamp'] = (int) $entry->timestamp;

            // Author
            $item['author'] = $key;
            
            // print_r($item);
            print('| "' . $item['title'] . '" from ' . $item['author'] . PHP_EOL);
            $old_article = db::table('articles')->where('id', $item['id'])->first();

            if ($old_article) {
                if ($old_article->misskey_note_id == null) {
                    $item['misskey_note_id'] = post_to_misskey($item['title'], $item['author'], $item['id']);
                }
                db::table('articles')->where('id', $item['id'])->update($item);
            } else {
                $item['misskey_note_id'] = post_to_misskey($item['title'], $item['author'], $item['id']);
                db::table('articles')->insert($item);
            }
        }

        print("DONE" . PHP_EOL . PHP_EOL);
        sleep(5);
    }
}

// Extract WP-JSON feeds
$keys = array_keys($feeds_wp_json);

if (!$youtube_only) for ($i = 0; $i < count($keys); $i++) {
    $key = $keys[$i];
    if (!is_array($feeds_wp_json[$key])) {
        $url = $feeds_wp_json[$key];
        $feeds_wp_json[$key] = [];
        array_push($feeds_wp_json[$key], $url);
    }

    // Calculate number of articles which should be fetched
    $multiplier = max(sqrt(sqrt(ceil(db::table('articles')->where('author', $key)->where('timestamp', '>=', ((int) strtotime($entry['date_gmt'] . " UTC")) - 30 * 24 * 60 * 60)->count()))), 1);

    for ($j = 0; $j < count($feeds_wp_json[$key]); $j++) {
        $url = $feeds_wp_json[$key][$j] . '&per_page=' . (5 * $multiplier);
        print('Extracting ' . $url . PHP_EOL);

        try {
            $entries = json_decode(implode(file($url)), true);
        } catch (Exception $e) {
            // Skip
            print("ERROR" . PHP_EOL);
            continue;
        }

        foreach ($entries as $entry) {
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

            if (isset($entry['_embedded']['wp:term'])) foreach ($entry['_embedded']['wp:term'] as $term) {
                if (count($term) == 0) continue;
                $term = $term[0];
                
                if ($term['taxonomy'] == 'category') switch ($term['slug']) {
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
            $old_article = db::table('articles')->where('id', $item['id'])->first();

            if ($old_article) {
                if ($old_article->misskey_note_id == null) {
                    $item['misskey_note_id'] = post_to_misskey($item['title'], $item['author'], $item['id']);
                }
                db::table('articles')->where('id', $item['id'])->update($item);
            } else {
                $item['misskey_note_id'] = post_to_misskey($item['title'], $item['author'], $item['id']);
                db::table('articles')->insert($item);
            }
        }
        
        db::table('articles')->where('summary', '')->update(['summary' => null]);
        db::table('articles')->where('content', '')->update(['content' => null]);

        print("DONE" . PHP_EOL . PHP_EOL);
        sleep(5);
    }
}
