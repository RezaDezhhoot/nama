<?php

return [
    'user' => [
        'status' => [
            'active' => 'Active',
            'deactivate' => 'Deactivate',
            'suspended' => 'Suspended'
        ],
        'visibility' => [
            'private' => 'Private',
            'public' => 'Public'
        ],
        'messages' => [
            'everyone' => 'Everyone',
            'my_followers' => 'My followers',
            'my_following' => 'My following'
        ],
        'mentions' => [
            'everyone' => 'Everyone',
            'my_followers' => 'My followers',
            'my_following' => 'My following'
        ]
    ],
    'penalty' => [
        'actions' => [
            'block' => 'Block',
            'notice' => 'Notice',
            'suspended' => 'Suspended'
        ]
    ],
    'ticket' => [
        'status' => [
            'pending' => 'Pending',
            'deactivated' => 'Deactivated',
            'admin' => 'Admin',
            'answered' => 'Answered'
        ]
    ],
    'market' => [
        'status' => [
            'published' => 'Published',
            'draft' => 'Draft',
        ]
    ],
    'post' => [
        'types' => [
            'post' => 'Post',
            'thread' => 'Thread'
        ],
        'visibilities' => [
            'public' => 'Public',
            'private' => 'Private'
        ],
        'schedule' => [
            'pending' => 'Pending',
            'running' => 'Running',
            'done' => 'Done',
            'error' => 'Error',
        ],
        'categories' => [
            'normal' => 'Normal',
            'awareness' => 'Awareness',
            'demand_action' => 'Demand Action',
        ],
        'status' => [
            'published' => 'Published',
            'draft' => 'Draft',
            'queued' => 'Queuing for publication',
        ]
    ],
    'category' => [
        'types' => [
            'podcast' => 'Podcast',
            'question' => 'Question'
        ]
    ],
    'faq' => [
        'types' => [
            'rule' => 'Rule',
            'guild' => 'Guild'
        ]
    ],
    'podcast' => [
        'status' => [
            'published' => 'Published',
            'draft' => 'Draft',
            'coming_soon' => 'Coming soon',
        ],
        'type' => [
            'serial' => 'Serial',
            'single' => 'Single',
        ],
        'content' => [
            'sound' => 'Sound',
            'video' => 'Video'
        ]
    ],
    'question' => [
        'status' => [
            'published' => 'Published',
            'draft' => 'Draft',
            'coming_soon' => 'Coming soon',
        ],
        'difficulty' => [
            'easy' => 'Easy',
            'normal' => 'Normal',
            'hard' => 'Hard',
        ]
    ],
    'subscription' => [
        'status' => [
            'published' => 'Published',
            'draft' => 'Draft',
        ],
        'discount' => [
            'const' => 'Const',
            'percent' => 'Percent'
        ]
    ],
    'invoice' => [
        'status' => [
            'pending' => 'Pending',
            'error' => 'Error',
            'paid' => 'Paid',
        ],
    ],
    'comment' => [
        'status' => [
            'published' => 'Published',
            'draft' => 'Draft'
        ],
    ],
    'competition' => [
        'types' => [
            'user_duel' => 'User duel competition',
            'user_public' => 'User public competition',
            'user_twenty_questions' => 'User twenty questions competition',
            'team_duel' => 'Team duel competition',
            'team_twenty_questions' => 'Team twenty questions competition',
        ]
    ],
    'home' => [
        'elements' => [
            'slider' => 'Slider show',
            'grid' => 'Grid show',
            'single' => 'Single model show'
        ],
        'content' => [
            'category' => 'Category',
            'product' => 'Product',
            'podcast' => 'Podcast',
            'banner' => 'Banner',
            'with_latest' => 'With latest',
            'with_oldest' => 'With oldest',
            'with_popular' => 'With popular',
        ],
        'type' => [
            'single_tab' => 'Single tab',
            'multiple_tab' => 'Multiple tab'
        ]
    ],
    'parameters' => [
        'name' => 'User name',
        'subscription' => 'Subscription title',
        'amount' => 'Subscription amount'
    ],
    'checkout' => [
        'pending' => 'Pending',
        'done' => 'Done',
        'failed' => 'failed'
    ],
    'task' => [
        'types' => [
            'user' => 'User',
            'team' => 'Team'
        ]
    ],
    'reward' => [
        'types' => [
            'user' => 'User',
            'team' => 'Team'
        ]
    ]
];
