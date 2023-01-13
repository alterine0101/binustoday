<div class="alert px-15" role="alert">
    <h4 class="alert-heading font-weight-bold">Share to your friends:</h4>
    <div class="d-flex flex-wrap justify-content-between my-10">
        <a class="text-white btn btn-square rounded-circle btn-lg" href="https://social-plugins.line.me/lineit/share?url=<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #06c755"><i class="fa-lg fab fa-line" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">LINE</span></a>
        <a class="text-white btn btn-square rounded-circle btn-lg" href="https://wa.me/send?text=%2A<?= urlencode($data[0]->title) ?>%2A%0A<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #25D366"><i class="fa-lg fab fa-whatsapp" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">WhatsApp</span></a>
        <a class="text-white btn btn-square rounded-circle btn-lg" href="https://twitter.com/share?text=<?= urlencode($data[0]->title) ?>&amp;url=<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #1DA1F2"><i class="fa-lg fab fa-twitter" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">Twitter</span></a>
        <a class="text-white btn btn-square rounded-circle btn-lg" href="https://t.me/share/url?text=<?= urlencode($data[0]->title) ?>&amp;url=<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #FE4500"><i class="fa-lg fab fa-telegram" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">Telegram</span></a>
        <a class="text-white btn btn-square rounded-circle btn-lg" href="https://www.facebook.com/sharer/sharer.php?u=<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #1977F2"><i class="fa-lg fab fa-facebook" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">Facebook</span></a>
        <a class="text-white btn btn-square rounded-circle btn-lg" href="https://www.linkedin.com/shareArticle?text=<?= urlencode($data[0]->title) ?>&amp;url=<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #0077B5"><i class="fa-lg fab fa-linkedin" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">LinkedIn</span></a>
        <a class="text-white btn btn-square rounded-circle btn-lg" href="mailto:?body=<?= urlencode($data[0]->title) ?> <?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>" target="_blank" style="background-color: #ff4d4f"><i class="fa-lg bi bi-envelope" aria-hidden="true" style="line-height: inherit;"></i><span class="sr-only">Email</span></a>
        <a class="btn btn-square rounded-circle btn-lg"><i class="fa-lg bi bi-three-dots" aria-hidden="true" onclick="try{navigator.share({title: '<?= filter_var($data[0]->title, FILTER_SANITIZE_STRING) ?>',url: '<?= 'http://' . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']) ?>'})} catch(e) {document.getElementById('share-modal-url').value = '<?= 'http://' . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'] ?>'; halfmoon.toggleModal('share-modal')}"></i><span class="sr-only">Aplikasi lainnya...</span></a>
    </div>
    <a onClick="document.getElementById('originalArticle').style.display = 'block'; document.getElementById('readerView').style.display = 'none';">View Original Article</a>
</div>