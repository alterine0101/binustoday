<?php
require_once('./dbconnection.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>';

$take = 100;
$count = db::table('articles')->selectRaw('count(*) as count')->first()->count;
header("X-BinusToday-TotalArticles: $count");
?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php
        /* Index 1 is always for authors */
        $total_indexes = (int) ceil($count / $take) + 1;
        header("X-BinusToday-TotalIndexes: $total_indexes");
        for ($i = 1; $i <= $total_indexes; $i++) {
            echo "<sitemap><loc>https://binustoday.reinhart1010.id/sitemap.php?page=$i</loc></sitemap>";
        }
    ?>
</sitemapindex>
