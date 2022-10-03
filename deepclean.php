<?php
require_once('./dbconnection.php');

print('Starting deep cleaning process...' . PHP_EOL);
$total_articles = db::table('articles')->count();
print("There are $total_articles articles available" . PHP_EOL);
$index = 0;
$removed = 0;

while ($index < $total_articles) {
    $TAKE = 10;
    $articles = db::table('articles')->select(['id'])->skip($index)->take(15)->get();
    foreach ($articles as $article) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $article->id);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_exec($ch);
        $is404 = curl_getinfo($ch, CURLINFO_HTTP_CODE) == 404;
        curl_close($ch);

        if ($is404) {
            db::table('articles')->where('id', $article->id)->delete();
            $removed++;
            print("X | Removed $article->id" . PHP_EOL);
        }

    }
    $index += 10;
    sleep(10);
}
print("Deep cleaning complete, with $removed posts removed" . PHP_EOL);
