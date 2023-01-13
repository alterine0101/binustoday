<?php
    $article_time = new DateTime();
    $article_time->setTimestamp($_SESSION['article']->timestamp);
    $now = new DateTime();
    $diff = $article_time->diff($now);
    if ($diff->days < 31) {
        echo ($diff->days > 0 ? ($diff->days . ' days ') : '') . $diff->h . ' hours ago';
    } else {
        echo $article_time->format('F j, Y');
    }
?>