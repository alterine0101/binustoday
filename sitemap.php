<?php
require_once('./dbconnection.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';

$page = 1;
if (isset($_GET['page']) && strlen($_GET['page']) > 0) $page = (int) filter_var($_GET['skip'], FILTER_SANITIZE_STRING);
$take = 100;

?>

<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">

    <?php

    if ($page <= 1) {
        echo "
        <url>
            <loc>https://binustoday.reinhart1010.id/</loc>
            <lastmod>" . gmdate("Y-m-d") . "</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.00</priority>
        </url>
        ";

        foreach (array_keys($authors) as $author){
            $url = 'https://binustoday.reinhart1010.id/?author=' . urlencode($author);
            $date = ($authors[$author] !== true) ? gmdate('Y-m-d', $authors[$author]) : gmdate('Y-m-d');
            echo "
            <url>
                <loc>$url</loc>
                <lastmod>$date</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.9</priority>
            </url>";
        }
    } else {
        $articles = db::table('articles')->skip(($page - 2) * $take)->take($take)->orderBy('timestamp', 'desc')->get();
        header('X-BinusToday-TotalArticles: ' . count($articles));
        
        foreach ($articles as $article){
            $date = gmdate("Y-m-d", $article->timestamp);
            $url = 'https://binustoday.reinhart1010.id/?a=' . urlencode($article->id);
            $change_freq = 'weekly';

            $article_time = new DateTime();
            $article_time->setTimestamp($article->timestamp);
            $now = new DateTime();
            $diff = $article_time->diff($now);

            if ($diff->days > 28){
                $change_freq = 'monthly';
            }
            if ($diff->days > 84){
                $change_freq = 'yearly';
            }
            if ($diff->days > 364){
                $change_freq = 'never';
            };

            if (isset($authors[$article->author]) && $authors[$article->author] === true){
                $authors[$article->author] = $article->timestamp;
            }

            echo "
            <url>
                <loc>$url</loc>
                <lastmod>$date</lastmod>
                <changefreq>$change_freq</changefreq>
                <priority>0.8</priority>
            </url>";
        }
    }
?>
</urlset>