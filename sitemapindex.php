<?php
require_once('./dbconnection.php');

header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" ?>';

$take = 100;
$count = db::table('articles')->selectRaw('count(*) as count')->get()->count;
?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php
        $total_indexes = (int) ceil($count / $take);
        for ($i = 1; $i <= $total_indexes; $i++) {
            echo "<sitemap><loc>sitemap.php?page=$i</loc></sitemap>"
        }
    ?>
</sitemapindex>
