<?php
// Classifies the feeds to extract for
$feeds = [
    // 'BINUS Group' => 'https://www.binus.edu/feed/atom/',
    'Bina Nusantara Computer Club (BNCC)' => [
        'https://student-activity.binus.ac.id/bncc/feed/atom/',
        'https://student-activity.binus.ac.id/bncc/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/bncc/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/bncc/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/bncc/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/bncc/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/bncc/gallery/feed/atom/?paged=2',
    ],
    'Bina Nusantara Mandarin Club (BNMC)' => [
        'https://student-activity.binus.ac.id/bnmc/feed/atom/',
        'https://student-activity.binus.ac.id/bnmc/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/bnmc/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/bnmc/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/bnmc/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/bnmc/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/bnmc/gallery/feed/atom/?paged=2',
    ],
    'BINUS Game Development Club (BGDC)' => [
        'https://student-activity.binus.ac.id/bgdc/feed/atom/',
        'https://student-activity.binus.ac.id/bgdc/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/bgdc/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/bgdc/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/bgdc/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/bgdc/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/bgdc/gallery/feed/atom/?paged=2',
    ],
    'BINUS International Office' => [
        'https://io.binus.ac.id/feed/atom/',
        'https://io.binus.ac.id/feed/atom/?paged=2',
        'https://io.binus.ac.id/feed/atom/?paged=3',
        'https://io.binus.ac.id/feed/atom/?paged=4',
        'https://io.binus.ac.id/feed/atom/?paged=5',
    ],
    'BINUS Square Student Community (BSSC)' => [
        'https://student-activity.binus.ac.id/bssc/feed/atom/',
        'https://student-activity.binus.ac.id/bssc/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/bssc/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/bssc/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/bssc/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/bssc/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/bssc/gallery/feed/atom/?paged=2',
    ],
    'BINUS TV' => [
        'https://www.youtube.com/feeds/videos.xml?channel_id=UCLxjqn6KmvveyFdh5GeObGw',
        'https://binus.tv/feed/atom/',
        'https://binus.tv/feed/atom/?paged=2',
        'https://binus.tv/feed/atom/?paged=3',
        'https://binus.tv/feed/atom/?paged=4',
        'https://binus.tv/feed/atom/?paged=5',
    ],
    'BINUS TV Club' => [
        'https://student-activity.binus.ac.id/binustvclub/feed/atom/',
        'https://student-activity.binus.ac.id/binustvclub/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/binustvclub/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/binustvclub/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/binustvclub/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/binustvclub/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/binustvclub/gallery/feed/atom/?paged=2',
    ],
    'BINUS University' => [
        'https://binus.ac.id/feed/atom/',
        'https://binus.ac.id/feed/atom/?paged=2',
        'https://binus.ac.id/feed/atom/?paged=3',
        'https://binus.ac.id/feed/atom/?paged=4',
        'https://binus.ac.id/feed/atom/?paged=5',
    ],
    'BINUS English Club (BNEC)' => [
        'https://student-activity.binus.ac.id/bnec/feed/atom/',
        'https://student-activity.binus.ac.id/bnec/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/bnec/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/bnec/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/bnec/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/bnec/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/bnec/gallery/feed/atom/?paged=2',
    ],
    'BVoice Radio' => [
        'https://student-activity.binus.ac.id/bvoice/feed/atom/',
        'https://student-activity.binus.ac.id/bvoice/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/bvoice/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/bvoice/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/bvoice/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/bvoice/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/bvoice/gallery/feed/atom/?paged=2',
    ],
    'Cyber Security Community (CSC)' => [
        'https://student-activity.binus.ac.id/csc/feed/atom/',
        'https://student-activity.binus.ac.id/csc/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/csc/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/csc/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/csc/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/csc/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/csc/gallery/feed/atom/?paged=2',
    ],
    'DKV Animation' => [
        'https://animation.binus.ac.id/feed/atom/',
        'https://animation.binus.ac.id/feed/atom/?paged=2',
        'https://animation.binus.ac.id/feed/atom/?paged=3',
        'https://animation.binus.ac.id/feed/atom/?paged=4',
        'https://animation.binus.ac.id/feed/atom/?paged=5',
    ],
    'DKV Creative Advertising' => [
        'https://ca.binus.ac.id/feed/atom/',
        'https://ca.binus.ac.id/feed/atom/?paged=2',
        'https://ca.binus.ac.id/feed/atom/?paged=3',
        'https://ca.binus.ac.id/feed/atom/?paged=4',
        'https://ca.binus.ac.id/feed/atom/?paged=5',
    ],
    'DKV New Media' => [
        'https://dkv.binus.ac.id/feed/atom/',
        'https://dkv.binus.ac.id/feed/atom/?paged=2',
        'https://dkv.binus.ac.id/feed/atom/?paged=3',
        'https://dkv.binus.ac.id/feed/atom/?paged=4',
        'https://dkv.binus.ac.id/feed/atom/?paged=5',
    ],
    'First Year Program (FYP)' => 'https://student.binus.ac.id/fyp/feed/atom/',
    'Himpunan Mahasiswa DKV (HIMDKV)' => [
        'https://student-activity.binus.ac.id/himdkv/feed/atom/',
        'https://student-activity.binus.ac.id/himdkv/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/himdkv/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/himdkv/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/himdkv/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/himdkv/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/himdkv/gallery/feed/atom/?paged=2',
    ],
    'Himpunan Mahasiswa Sastra Jepang (HIMJA)' => [
        'https://student-activity.binus.ac.id/himja/feed/atom/',
        'https://student-activity.binus.ac.id/himja/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/himja/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/himja/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/himja/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/himja/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/himja/gallery/feed/atom/?paged=2',
    ],
    'Himpunan Mahasiswa Sastra Mandarin (HIMANDA)' => [
        'https://student-activity.binus.ac.id/himanda/feed/atom/',
        'https://student-activity.binus.ac.id/himanda/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/himanda/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/himanda/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/himanda/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/himanda/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/himanda/gallery/feed/atom/?paged=2',
    ],
    'Himpunan Mahasiswa Sistem Informasi (HIMSISFO)' => [
        'https://student-activity.binus.ac.id/himsisfo/feed/atom/',
        'https://student-activity.binus.ac.id/himsisfo/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/himsisfo/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/himsisfo/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/himsisfo/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/himsisfo/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/himsisfo/gallery/feed/atom/?paged=2',
    ],
    'Himpunan Mahasiswa Teknik Informatika (HIMTI)' => [
        'https://www.youtube.com/feeds/videos.xml?channel_id=UCnxjFlgW3YpKVkSshFHP_SQ',
        'https://student-activity.binus.ac.id/himti/feed/atom/',
        'https://student-activity.binus.ac.id/himti/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/himti/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/himti/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/himti/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/himti/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/himti/gallery/feed/atom/?paged=2',
    ],
    'Nippon Club' => [
        'https://nipponclub.net/rss/',
        'https://student-activity.binus.ac.id/nc/feed/atom/',
        'https://student-activity.binus.ac.id/nc/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/nc/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/nc/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/nc/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/nc/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/nc/gallery/feed/atom/?paged=2',
    ],
    'Student BINUS' => [
        'https://www.youtube.com/feeds/videos.xml?channel_id=UCbObjb4NF91_t6csfgT4Bvg',
        'https://student.binus.ac.id/feed/atom/',
        'https://student.binus.ac.id/feed/atom/?paged=2',
        'https://student.binus.ac.id/feed/atom/?paged=3',
        'https://student.binus.ac.id/feed/atom/?paged=4',
        'https://student.binus.ac.id/feed/atom/?paged=5',
    ],
    'Student Club and Activity Center (SCAC)' => [
        'https://student-activity.binus.ac.id/feed/atom/',
        'https://student-activity.binus.ac.id/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/feed/atom/?paged=5',
    ],
    'School of Computer Science (SOCS)' => [
        'https://socs.binus.ac.id/feed/atom/',
        'https://socs.binus.ac.id/feed/atom/?paged=2',
        'https://socs.binus.ac.id/feed/atom/?paged=3',
        'https://socs.binus.ac.id/feed/atom/?paged=4',
        'https://socs.binus.ac.id/feed/atom/?paged=5',
    ],
    'School of Information Systems (SIS)' => [
        'https://sis.binus.ac.id/feed/atom/',
        'https://sis.binus.ac.id/feed/atom/?paged=2',
        'https://sis.binus.ac.id/feed/atom/?paged=3',
        'https://sis.binus.ac.id/feed/atom/?paged=4',
        'https://sis.binus.ac.id/feed/atom/?paged=5',
    ],
    'Teach for Indonesia (TFI)' => [
        'http://www.teachforindonesia.org/feed/atom/',
        'http://www.teachforindonesia.org/feed/atom/?paged=2',
        'http://www.teachforindonesia.org/feed/atom/?paged=3',
        'http://www.teachforindonesia.org/feed/atom/?paged=4',
        'http://www.teachforindonesia.org/feed/atom/?paged=5',
    ],
    'TFI Student Committee (TFISC)' => [
        'https://student-activity.binus.ac.id/tfi/feed/atom/',
        'https://student-activity.binus.ac.id/tfi/feed/atom/?paged=2',
        'https://student-activity.binus.ac.id/tfi/feed/atom/?paged=3',
        'https://student-activity.binus.ac.id/tfi/feed/atom/?paged=4',
        'https://student-activity.binus.ac.id/tfi/feed/atom/?paged=5',
        'https://student-activity.binus.ac.id/tfi/gallery/feed/atom/',
        'https://student-activity.binus.ac.id/tfi/gallery/feed/atom/?paged=2',
    ],
];

// Classifies feeds which supports the WP-JSON APIs
$feeds_wp_json = [
    'BVoice Radio' => 'https://www.bvoiceradio.com/wp-json/wp/v2',
    'Filemagz by BNCC' => 'https://www.filemagz.com/wp-json/wp/v2',
];
