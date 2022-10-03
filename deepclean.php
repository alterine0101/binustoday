<?php
require_once('./dbconnection.php');

print('Starting deep cleaning process...' . PHP_EOL);
// $total_articles = db::table('articles')->count();
$total_articles = 2000;
print("There are $total_articles articles available" . PHP_EOL);
$index = 0;
$removed = 0;

while ($index < $total_articles) {
    $TAKE = 10;
    $articles = db::table('articles')->select(['id'])->skip($index)->take(15)->orderBy('timestamp', 'desc')->get();
    $currently_removed = 0;
    foreach ($articles as $article) {
        $test = get_headers($article->id);

        if (!str_ends_with($test[0], '200 OK')) {
            db::table('articles')->where('id', $article->id)->delete();
            $currently_removed++;
            print("X | Removed $article->id" . PHP_EOL);
        }

    }
    $index += 10 - $currently_removed;
    $removed += $currently_removed;
    sleep(10);
}
print("Deep cleaning complete, with $removed posts removed" . PHP_EOL);
