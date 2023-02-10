<?php

return [
    /**
     * Transistor API Key
     */
    'api_key' => env('TRANSISTOR_API_KEY'),

    /**
     * If you've renamed the collections in your admin panel, you
     * can update these values so everything is kept in sync
     */
    'collections' => [
        'show' => env('TRANSISTOR_COLLECTION_SHOW', 'podcast_show'),
        'episode' => env('TRANSISTOR_COLLECTION_EPISODE', 'podcast_episode'),
    ],
];
