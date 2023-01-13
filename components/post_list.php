<?php $article_count = 0; ?>
<?php foreach ($_SESSION['articles'] as $article): ?>
    <?php $article_count++; ?>
    <?php if ($article_count % 5 == 0): ?>
        <!-- BINUSToday Sponsored Card -->
        <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-6503953249125893" data-ad-slot="5841985741" data-ad-format="auto" data-full-width-responsive="true"></ins>
    <?php endif; ?>
    <a class="card my-10 mx-0 p-0 text-decoration-none" href="/?a=<?= urlencode($article->id) ?>">
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
                <b>
                    <?php switch($article->type) {
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
                    } ?><?= $article->type ?></span>
                    <?= $article->author ?>
                </b> &bull;
                <?php require('./article_time.php'); ?>
            </p>
            <h4 class="font-weight-bold"><?= $article->title ?></h4>
        </div>
    </a>
<?php endforeach; ?>