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

$capsule->setEventDispatcher(new Dispatcher(new Container));
$capsule->setAsGlobal();

$loadArticle = false;
$search = false;
$authorSearch = false;
$index = 1;
if (isset($_GET['q'])) $search = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
if (isset($_GET['author'])) $search = filter_var($_GET['author'], FILTER_SANITIZE_STRING);
if (isset($_GET['p']) && (int) $_GET['p'] > 0) $index = (int) $_GET['p'];
if (isset($_GET['a'])) $loadArticle = filter_var($_GET['a'], FILTER_SANITIZE_URL);

$limit = 25;
$offset = ($index - 1) * $limit;

$data = db::table('articles');

if ($loadArticle === false){
    if ($search !== false){
        $search = '%' . str_replace(' ', '%', $search) . '%';
        $data = $data->where('summary', 'LIKE', $search);
    } else if ($authorSearch !== false){
        $data = $data->where('author', $authorSearch);
    }

    $data = $data->skip($offset)->take($limit)->orderBy('timestamp', 'desc')->get();
} else {
    $data = $data->where('id', $loadArticle)->get();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
    <meta name="viewport" content="width=device-width" />

    <!-- Favicon and title -->
    <!-- <link rel="icon" href="path/to/fav.png"> -->
    <title>BINUS Today</title>

    <!-- Halfmoon CSS -->
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.1/css/halfmoon.min.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

    <style>
        body {
            font-size: 1.6rem;
            font-family: "Open Sans",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
        }
        #card-container {
            column-count: 3;
        }

        #card-container > * {
            -webkit-column-break-inside: avoid;
            display: inline-block;
            width: 100% !important;
            justify-items: center;
        }

        @media (max-width: 1199px){
            #card-container {
                column-count: 2;
            }
        }
        @media (max-width: 991px){
            #card-container {
                column-count: 1;
            }
        }
        .placeholder-image {
            width: 100%;
        }
        .placeholder-image img {
            max-width: 80%;
        }
        body:not(.dark-mode) .placeholder-image {
            background-color: #34AEE2;
        }
        body:not(.dark-mode) .placeholder-image.binus-tv {
            background-color: #FFE53A;
        }
        body:not(.dark-mode) .placeholder-image.bvoice-radio {
            background-color: #FAD620;
        }
        body:not(.dark-mode) .placeholder-image.filemagz {
            background-color: #000000;
        }
        
        .wp-block-image img {
            width: 100%;
            height: auto;
        }
    </style>
    </head>
    <body class="with-custom-webkit-scrollbars with-custom-css-scrollbars" data-dm-shortcut-enabled="true" data-sidebar-shortcut-enabled="true" data-set-preferred-mode-onload="true">
    <!-- Modals go here -->
    <!-- Reference: https://www.gethalfmoon.com/docs/modal -->

    <!-- Page wrapper start -->
    <div class="page-wrapper with-navbar with-sidebar">

        <!-- Sticky alerts (toasts), empty container -->
        <!-- Reference: https://www.gethalfmoon.com/docs/sticky-alerts-toasts -->
        <div class="sticky-alerts"></div>

        <!-- Navbar start -->
        <nav class="navbar">
            <div class="navbar-content">
                <button class="btn btn-action" type="button" onclick="halfmoon.toggleSidebar()">
                    <i class="bi bi-list" aria-hidden="true"></i>
                    <span class="sr-only">Toggle sidebar</span>
                </button>
            </div>
            <a href="/" class="navbar-brand">
                <b>BINUS</b>Today
            </a>
            <form class="form-inline d-none d-md-flex ml-auto" action="/" method="GET"> <!-- d-none = display: none, d-md-flex = display: flex on medium screens and up (width > 768px), ml-auto = margin-left: auto -->
                <input type="text" name="q" class="form-control" placeholder="Search News and Articles..." required="required">
                <button class="btn btn-action btn-primary" type="submit">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <span class="sr-only">Search</span>
                </button>
            </form>
            <div class="navbar-content d-md-none ml-auto"> <!-- d-md-none = display: none on medium screens and up (width > 768px), ml-auto = margin-left: auto -->
                <div class="dropdown with-arrow">
                    <button class="btn btn-action" data-toggle="dropdown" type="button" id="navbar-dropdown-toggle-btn-1">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <span class="sr-only">Search</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right w-200" aria-labelledby="navbar-dropdown-toggle-btn-1"> <!-- w-200 = width: 20rem (200px) -->
                        <div class="dropdown-content">
                            <form action="/" method="GET">
                                <div class="form-group">
                                <input type="text" name="q" class="form-control" placeholder="Search News and Articles..." required="required">
                                </div>
                                <button class="btn btn-primary btn-block" type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Navbar end -->

        <!-- Sidebar start -->
        <div class="sidebar">
            <div class="sidebar-menu">
                <!-- Sidebar links and titles -->
                <h5 class="sidebar-title">Menu</h5>
                <div class="sidebar-divider"></div>
                <a href="/" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon">
                        <i class="bi bi-house" aria-hidden="true"></i>
                    </span>
                    Home
                </a>
                <br>
                <h5 class="sidebar-title">Authors</h5>
                <div class="sidebar-divider"></div>
                <?php foreach(array_keys($feeds) as $feed): ?>
                    <a href="/?author=<?= $feed ?>" class="sidebar-link" style="font-weight:600;">
                        <?= $feed ?> <i class="bi bi-arrow-right-circle"></i>
                    </a>
                <?php endforeach; ?>
                <br>
                <h5 class="sidebar-title">About</h5>
                <div class="sidebar-divider"></div>
                <a href="#" class="sidebar-link">Version</a>
                <a href="#" class="sidebar-link">Donate</a>
            </div>
        </div>
        <!-- Sidebar end -->

        <!-- Content wrapper start -->
        <div class="content-wrapper<?= ($loadArticle !== false && strlen($data[0]->content) == 0) ? ' overflow-hidden' : ''?>">
            <?php if ($loadArticle === false): ?>
                <div class="container">
                    <?php if ($search !== false): ?>
                        <h1 class="p-20 m-0 pb-0">Search results for <b><?= $_GET['q'] ?></b></h1>
                    <?php endif; ?>
                    <div id="card-container" class="p-20">
                        <?php foreach ($data as $article): ?>
                            <a class="card my-10 mx-0 p-0 text-decoration-none" href="/?a=<?= $article->id ?>">
                                <?php if (strlen($article->cover_image) > 0): ?>
                                    <img style="width: 100%; height: auto" src="<?= $article->cover_image ?>" class="mb-10">
                                <?php else: ?>
                                    <div style="width: 100%" class="h-150 mb-10 align-self-center">
                                        <?php if (str_starts_with($article->id, 'https://www.binus.tv/')): ?>
                                            <div class="placeholder-image binus-tv h-150 d-flex align-items-center justify-content-center">
                                                <img src="https://www.binus.tv/wp-content/themes/binus-2014-58-core/assets/university/site-logo/binustv/site-logo.png">
                                            </div>
                                        <?php elseif (str_starts_with($article->id, 'https://www.bvoiceradio.com/')): ?>
                                            <div class="placeholder-image bvoice-radio h-150 d-flex align-items-center justify-content-center">
                                                <img src="https://www.bvoiceradio.com/wp-content/uploads/2021/04/cropped-Logo-BVoice-2-1-1536x759.png">
                                            </div>
                                        <?php elseif (str_starts_with($article->id, 'https://www.filemagz.com/')): ?>
                                            <div class="placeholder-image filemagz h-150 d-flex align-items-center justify-content-center">
                                                <img src="https://www.filemagz.com/wp-content/uploads/2021/03/FILEMagz-White.png">
                                            </div>
                                        <?php else: ?>
                                            <div class="placeholder-image h-150 d-flex align-items-center justify-content-center">
                                                <img src="https://binus.ac.id/wp-content/themes/binus-2017-core/view/default-image/binus-2017/images/univ/binus-logo-white.png">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="px-20">
                                    <p class="m-0">
                                        <b><?= $article->author ?></b> &bull;
                                        <?php
                                            $article_time = new DateTime();
                                            $article_time->setTimestamp($article->timestamp);
                                            $now = new DateTime();
                                            $diff = $article_time->diff($now);
                                            echo ($diff->days > 0 ? ($diff->days . ' days ') : '') . $diff->h . ' hours ago';
                                        ?>
                                    </p>
                                    <h4 class="font-weight-bold"><?= $article->title ?></h4>
                                </div>
                            </a>

                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <iframe <?= strlen($data[0]->content) > 0 ? 'class="d-none"' : '' ?> style="width: 100%; height: 100%; border:0;" src="<?= $data[0]->id ?>?utm_source=binustoday&utm_campaign=binustodayarticleview"></iframe>
                <?php if (strlen($data[0]->content) > 0): ?>
                    <?php if (strlen($data[0]->cover_image) > 0): ?>
                        <img style="width: 100%; height: auto" src="<?= $data[0]->cover_image ?>">
                    <?php endif; ?>
                    <article class="content m-auto p-20" style="max-width: 50rem">
                        <h1 class="font-weight-bold"><?= $data[0]->title ?></h1>
                        <h5>
                            By <b><?= $data[0]->author ?></b> &bull;
                            <?php
                                $article_time = new DateTime();
                                $article_time->setTimestamp($data[0]->timestamp);
                                $now = new DateTime();
                                $diff = $article_time->diff($now);
                                echo ($diff->days > 0 ? ($diff->days . ' days ') : '') . $diff->h . ' hours ago';
                            ?>
                        </h5>
                        <div id="articlecontent">
                            <?= $data[0]->content ?>
                        </div>
                        <a href="<?= $data[0]->id ?>?utm_source=binustoday&utm_campaign=binustodayvieworiginal">View Original Article</a>
                        <script src="assets/beautify-article.js"></script>
                    </article>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <!-- Content wrapper end -->

    </div>
    <!-- Page wrapper end -->

    <!-- Halfmoon JS -->
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.1/js/halfmoon.min.js"></script>
</body>
</html>