<?php
require_once('./dbconnection.php');

print('Starting deep cleaning process...' . PHP_EOL);

$skip = getenv('DEEPCLEAN_SKIP');
$skip = $skip == false ? 0 : ((int)$skip);

$total_articles = getenv('DEEPCLEAN_TAKE');
$total_articles = ($total_articles == false ||  (int)$total_articles <= 0) ? db::table('articles')->count() : (int)$total_articles;

$filter = getenv('DEEPCLEAN_FILTER');
if ($filter != false) {
  $filter = stripslashes(mysql_real_escape_string($filter));
}

print("Skipping $skip articles" . PHP_EOL);
print("There are $total_articles articles available" . PHP_EOL);
$index = 0;
$removed = 0;

while ($index < $total_articles) {
    $TAKE = 10;
    $articles = db::table('articles')->select(['id'])->skip($skip + $index);
    if ($filter != false) {
        $articles->whereRaw("id LIKE $filter");
    }
    $articles = $articles->take(15)->orderBy('timestamp', 'desc')->get();
    $currently_removed = 0;
    foreach ($articles as $article) {
        $test = get_headers($article->id);

        if (str_contains($test[0], '404')) {
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
