<?php
return ['MeCms/Photos' => [
    //Admin layout
    'admin' => [
        //Number of photos to show per page. This must be a multiple of 4
        'photos' => 12,
     ],
    //Default layout
    'default' => [
        //Number of albums to show per page.
        //This must be a multiple of 3
        'albums' => 15,
        //Number of photos to show per page.
        //This must be a multiple of 4
        'photos' => 20,
    ],
    //Sitemap
    'sitemap' => [
        //Includes photos (photos and albums) in the sitemap
        'photos' => true,
    ],
]];
