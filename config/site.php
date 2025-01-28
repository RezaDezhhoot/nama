<?php

return [
    'files' => [
        'video' => [
            'formats' => explode(",",env("VALID_VIDEO_FORMATS","mp4,avi,mkv,mov,wmv,m4v,gif"))
        ],
        'audio' => [
            'formats' => explode(",",env("VALID_AUDIO_FORMATS","mp3,wav,aac,aa,aiff,flac,m4a,ogg,wma,ape"))
        ],
        'image' => [
            'formats' => explode(",",env("VALID_IMAGE_FORMATS","jpg,jpeg,png,gif"))
        ],
        'document' => [
            'formats' => explode(",",env("VALID_DOCUMENT_FORMATS","pptx,pdf,docx,xlsx,cfb"))
        ],
    ],
    'default_disk' => env('DEFAULT_DISK','public'),
];
