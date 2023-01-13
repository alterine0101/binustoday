<?php
require_once('./dbconnection.php');

$type = 'ALL-OTHER';
$load_article = false;
$search = false;
$author_search = false;
$index = 1;
if (isset($_GET['type']) && strlen($_GET['type']) > 0) $type = filter_var($_GET['type'], FILTER_SANITIZE_STRING);
if (isset($_GET['q']) && strlen($_GET['q']) > 0) $search = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
if (isset($_GET['author']) && strlen($_GET['author']) > 0) $author_search = filter_var($_GET['author'], FILTER_SANITIZE_STRING);
if (isset($_GET['p']) && (int) $_GET['p'] > 0) $index = (int) $_GET['p'];
if (isset($_GET['a']) && strlen($_GET['a']) > 0) $load_article = filter_var($_GET['a'], FILTER_SANITIZE_URL);

$limit = 50;
$offset = ($index - 1) * $limit;

$data = db::table('articles');
$not_found = false;

if ($load_article === false) {
    if ($search !== false) {
        $search = '%' . str_replace(' ', '%', $search) . '%';
        $data = $data->where('summary', 'LIKE', $search);
    } else if ($author_search !== false) {
        $data = $data->where('author', $author_search);
    } else if ($type == 'ALL-OTHER') {
        // Skip filter
        $data = $data;
    } else if ($type == 'NEWS-ARTICLES') {
        $data = $data->where('type', 'ARTICLE')->orWhere('type', 'NEWS');
    } else {
        $data = $data->where('type', strtoupper($type));
    }

    $data = $data->skip($offset)->take($limit)->orderBy('timestamp', 'desc')->get();
} else {
    $data = $data->where('id', $load_article)->get();
}
if (count($data) == 0) {
    http_response_code(404);
    $not_found = true;
}

$html_title = 'BINUS Today';
$html_description = 'Trending news and articles from over 100 departments, faculties, schools, and student organizations at BINUS University.';
$html_og_cover = 'https://binustoday.reinhart1010.id/assets/og-cover.jpg';
$html_canonical = 'https://binustoday.reinhart1010.id/';

if (count($data) > 0) {
    if ($load_article !== false) {
        $html_title = $data[0]->title . ' - ' . $html_title;
        $html_description = substr(strip_tags($data[0]->summary), 0, 160);
        if (strlen($data[0]->cover_image) > 0) $html_og_cover = $data[0]->cover_image;
        $html_canonical = $data[0]->id;
        $more_articles_by_author = db::table('articles')->where('author', $data[0]->author)->where('id', 'not', $data[0]->id)->take(5)->orderBy('timestamp', 'desc')->get();
        $more_articles_by_others = db::table('articles')->where('author', 'not', $data[0]->author)->where('id', 'not', $data[0]->id)->take(5)->orderBy('timestamp', 'desc')->get();
    } else if ($search !== false) {
        $html_title = 'Search results for ' . $search . ' - ' . $html_title;
    } else if ($author_search !== false) {
        $html_title = 'Posts published by ' . $author_search . ' - ' . $html_title;
        $html_canonical = 'https://binustoday.reinhart1010.id/?author=' . urlencode($author_search);
    }
}

function generate_url($p) {
    $res = '/index.php?p=' . $p;
    $keys = array_keys($_GET);
    foreach ($keys as $key) {
        if ($key != 'p') $res .= '&' . $key . '=' . urlencode($_GET[$key]);
    }
    return $res;
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
    
    <!-- Primary Meta Tags -->
    <title><?= $html_title ?></title>
    <meta name="title" content="<?= $html_title ?>">
    <meta name="description" content="<?= $html_description ?>">

    <!-- Favicon and title -->
    <link rel="icon" href="https://binustoday.reinhart1010.id/assets/favicon.svg" sizes="any" type="image/svg+xml">
    <link rel="canonical" href="<?= $html_canonical ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="BINUS Today">
    <meta property="og:url" content="<?= 'https://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>">
    <meta property="og:title" content="<?= $html_title ?>">
    <meta property="og:description" content="<?= $html_description ?>">
    <meta property="og:image" content="<?= $html_og_cover ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= 'https://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>">
    <meta property="twitter:title" content="<?= $html_title ?>">
    <meta property="twitter:description" content="<?= $html_description ?>">
    <meta property="twitter:image" content="<?= $html_og_cover ?>">

    <!-- Halfmoon CSS -->
    <link href="https://cdn.jsdelivr.net/npm/halfmoon@1.1.1/css/halfmoon.min.css" rel="stylesheet" />
    <link href="https://www.w3schools.com/lib/w3-colors-ios.css" rel="stylesheet" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">

    <!-- FontAwesome 5 Brands -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.1.1/css/all.min.css">
    
    <!-- Google AdSense JS -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6503953249125893" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="/assets/style.css" type="text/css" />
</head>
<body class="with-custom-webkit-scrollbars with-custom-css-scrollbars" data-dm-shortcut-enabled="true" data-sidebar-shortcut-enabled="true" data-set-preferred-mode-onload="true">
    <!-- Modals go here -->
    <div class="modal" id="share-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <a onclick="halfmoon.toggleModal('share-modal')" class="close" role="button" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </a>
                <h5 class="modal-title">Share via link:</h5>
                <div class="input-group">
                    <input type="text" class="form-control" id="share-modal-url">
                    <div class="input-group-append">
                        <button class="btn font-weight-medium" type="button" onclick="document.getElementById('share-modal-url').select(); document.execCommand('copy');"><i class="bi bi-clipboard" aria-hidden="true"></i> Salin</button>
                    </div>
                </div>
                <div class="text-right mt-20">
                    <a onclick="halfmoon.toggleModal('share-modal')" class="btn btn-primary font-weight-medium" role="button">Tutup</a>
                </div>
            </div>
        </div>
    </div>

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
                <input type="text" name="q" class="form-control" placeholder="Search..." required="required">
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
                                <input type="text" name="q" class="form-control" placeholder="Search..." required="required">
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
                    All/Other
                </a>
                <a href="/?type=NEWS-ARTICLES" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon text-white bg-primary">
                        <i class="bi bi-newspaper" aria-hidden="true"></i>
                    </span>
                    News & Articles
                </a>
                <a href="/?type=GALLERY" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon text-dark bg-success">
                        <i class="bi bi-images" aria-hidden="true"></i>
                    </span>
                    Gallery
                </a>
                <a href="/?type=PODCAST" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon text-dark bg-secondary">
                        <i class="bi bi-music-note-list" aria-hidden="true"></i>
                    </span>
                    Podcasts
                </a>
                <a href="/?type=VIDEO" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon text-white bg-danger">
                        <i class="bi bi-play-circle" aria-hidden="true"></i>
                    </span>
                    Videos
                </a>
                <a href="#" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon">
                        <i class="bi bi-calendar2-week" aria-hidden="true"></i>
                    </span>
                    Events (Coming Soon)
                </a>
                <a href="#" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon">
                        <i class="bi bi-app-indicator"></i>
                    </span>
                    Mobile App (Coming Soon)
                </a>
                <br>
                <h5 class="sidebar-title">About</h5>
                <div class="sidebar-divider"></div>
                <a href="#" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon">
                        <i class="bi bi-question-circle" aria-hidden="true"></i>
                    </span>
                    FAQ
                </a>
                <a href="#" class="sidebar-link sidebar-link-with-icon">
                    <span class="sidebar-icon">
                        <i class="bi bi-flag" aria-hidden="true"></i>
                    </span>
                    Report Feed/Article
                </a>
                <a href="https://github.com/reinhart1010/binustoday" class="sidebar-link sidebar-link-with-icon" target="_blank">
                    <span class="sidebar-icon">
                        <i class="bi bi-code-slash" aria-hidden="true"></i>
                    </span>
                    GitHub <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                </a>
                <a href="https://saweria.co/reinhart1010" class="sidebar-link sidebar-link-with-icon" target="_blank">
                    <span class="sidebar-icon">
                        <i class="bi bi-cash-coin" aria-hidden="true"></i>
                    </span>
                    Donate <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                </a>
                <br>
                <!-- In-Feed Ad -->
                <ins class="adsbygoogle" style="display:block" data-ad-format="fluid" data-ad-layout-key="-gc+3r+68-9q-29" data-ad-client="ca-pub-6503953249125893" data-ad-slot="9090140234"></ins>
                <br>
                <h5 class="sidebar-title">Authors</h5>
                <?php foreach(array_keys($authors) as $feed): ?>
                    <div class="sidebar-divider"></div>
                    <a href="/?author=<?= str_replace('&', '%26', $feed) ?>" class="sidebar-link" style="font-weight:600;">
                        <?= $feed ?> <i class="bi bi-arrow-right-circle"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Sidebar end -->

        <!-- Content wrapper start -->
        <div class="content-wrapper<?= ($load_article !== false && strlen($data[0]->content) == 0) ? ' overflow-hidden' : ''?>">
            <?php if ($not_found): ?>
                <div class="container">
                    <h1>404: Not Found</h1>
                    <!-- Multiplex Ad -->
                    <ins class="adsbygoogle" style="display:block" data-ad-format="autorelaxed" data-ad-client="ca-pub-6503953249125893" data-ad-slot="2529674116"></ins>
                </div>
            <?php elseif ($load_article === false): ?>
                <div class="container">
                    <?php if ($search !== false): ?>
                        <h1 class="p-20 m-0 pb-0">Search results for <b><?= $_GET['q'] ?></b></h1>
                    <?php endif; ?>
                    <?php if ($author_search !== false): ?>
                        <h1 class="p-20 m-0 pb-0">Posts published by <b><?= $_GET['author'] ?></b></h1>
                    <?php endif; ?>
                    <div id="card-container" class="p-20">
                        <?php if ($index > 1): ?>
                            <a class="card my-10 mx-0 p-10 text-decoration-none" href="<?= generate_url($index - 1) ?>"><b><i class="bi bi-arrow-left-circle" aria-hidden="true"></i> Go to previous page</b></a>
                        <?php endif; ?>
                        <?php
                            $_SESSION['articles'] = $data;
                            require('components/post_list');
                        ?>
                        <a class="card my-10 mx-0 p-10 text-decoration-none" href="<?= generate_url($index + 1) ?>"><b>Go to next page <i class="bi bi-arrow-right-circle" aria-hidden="true"></i></b></a>
                    </div>
                </div>
            <?php else: ?>
                <?php $should_force_iframe = strpos($data[0]->id, 'youtube.com/embed/') !== false || strlen($data[0]->content) == 0; ?>
                <iframe id="originalArticle" style="width: 100%; height: 100%; border:0;<?= $should_force_iframe ? '' : ' display: none' ?>" src="<?= $data[0]->id ?>?utm_source=binustoday&utm_campaign=binustodayarticleview" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <?php if (!$should_force_iframe): ?>
                    <article id="readerView">
                        <?php if (strlen($data[0]->cover_image) > 0): ?>
                            <img style="width: 100%; height: auto" src="<?= $data[0]->cover_image ?>">
                        <?php endif; ?>
                        <div class="content m-auto p-20" style="max-width: 50rem">
                            <p class="m-0">
                                <b>
                                    <?php switch($data[0]->type) {
                                        case 'ARTICLE':
                                        case 'NEWS':
                                            echo '<span class="badge badge-primary"><i class="bi bi-newspaper" aria-hidden="true"></i> ';
                                            break;
                                        case 'GALLERY':
                                            echo '<span class="badge badge-success"><i class="bi bi-images" aria-hidden="true"></i> ';
                                            break;
                                        case 'PODCAST':
                                            echo '<span class="badge badge-secondary"><i class="bi bi-music-note-list" aria-hidden="true"></i> ';
                                            break;
                                        case 'VIDEO':
                                            echo '<span class="badge badge-danger"><i class="bi bi-play-circle" aria-hidden="true"></i> ';
                                            break;
                                        default:
                                            echo '<span class="badge">';
                                    } ?><?= $data[0]->type ?></span>
                                </b>
                            </p>
                            <h1 class="font-weight-bold article-title"><?= $data[0]->title ?></h1>
                            <h5>
                                By <a href="/?author=<?= $data[0]->author ?>"><b><?= $data[0]->author ?></b></a> &bull;
                                <?php require('components/article_time.php'); ?>
                            </h5>
                            <?php require('components/share_sheet.php'); ?>
                            <div id="articlecontent">
                                <?= $data[0]->content ?>
                            </div>
                            <?php require('components/share_sheet.php'); ?>
                            <script src="assets/beautify-article.js"></script>
                            <!-- Multiplex Ad -->
                            <ins class="adsbygoogle" style="display:block" data-ad-format="autorelaxed" data-ad-client="ca-pub-6503953249125893" data-ad-slot="2529674116"></ins>
                            <!-- Flex direction row -->
                            <div class="container-fluid">
                                <h2>More Articles</h2>
                                <div class="row">
                                    <div class="col-sm">
                                        <h5>From <?= $data[0]->author ?></h5>
                                        <?php
                                            $_SESSION['articles'] = $more_articles_by_author;
                                            require('components/post_list.php');
                                        ?>
                                    </div>
                                    <div class="col-sm">
                                        <h5>From others</h5>
                                        <?php
                                            $_SESSION['articles'] = $more_articles_by_author;
                                            require('components/post_list.php');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <!-- Content wrapper end -->

    </div>
    <!-- Page wrapper end -->

    <!-- Halfmoon JS -->
    <script src="https://cdn.jsdelivr.net/npm/halfmoon@1.1.1/js/halfmoon.min.js"></script>

    <!-- Google AdSense JS -->
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</body>
</html>
