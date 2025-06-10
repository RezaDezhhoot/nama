<?php

return [
    'user' => [
        'status' => [
            'active' => 'فعال',
            'deactivate' => 'غیرفعال کردن',
            'suspended' => 'معلق'
        ],
        'visibility' => [
            'private' => 'خصوصی',
            'public' => 'عمومی'
        ],
        'messages' => [
            'everyone' => 'همه',
            'my_followers' => 'دنبال‌کنندگان من',
            'my_following' => 'دنبال‌شده‌های من'
        ],
        'mentions' => [
            'everyone' => 'همه',
            'my_followers' => 'دنبال‌کنندگان من',
            'my_following' => 'دنبال‌شده‌های من'
        ]
    ],
    'penalty' => [
        'actions' => [
            'block' => 'مسدود کردن',
            'notice' => 'اخطار',
            'suspended' => 'معلق'
        ]
    ],
    'ticket' => [
        'status' => [
            'pending' => 'در انتظار',
            'deactivated' => 'غیرفعال شده',
            'admin' => 'مدیر',
            'answered' => 'پاسخ داده شده'
        ]
    ],
    'market' => [
        'status' => [
            'published' => 'منتشر شده',
            'draft' => 'پیش‌نویس',
        ]
    ],
    'post' => [
        'types' => [
            'post' => 'پست',
            'thread' => 'موضوع'
        ],
        'visibilities' => [
            'public' => 'عمومی',
            'private' => 'خصوصی'
        ],
        'schedule' => [
            'pending' => 'در انتظار',
            'running' => 'در حال اجرا',
            'done' => 'انجام شده',
            'error' => 'خطا',
        ],
        'categories' => [
            'normal' => 'عادی',
            'awareness' => 'آگاهی',
            'demand_action' => 'نیاز به اقدام',
        ],
        'status' => [
            'published' => 'منتشر شده',
            'draft' => 'پیش‌نویس',
            'queued' => 'در صف انتشار',
        ]
    ],
    'competition' => [
        'types' => [
            'user_duel' => 'رقابت تکی کاربران',
            'user_public' => 'رقابت عمومی کاربران',
            'user_twenty_questions' => 'رقابت 20 سوالی کاربران',
            'team_duel' => 'رقابت تکی تیم ها',
            'team_twenty_questions' => 'رقابت 20 سوالی تیم ها',
        ]
    ],
    'category' => [
        'types' => [
            'podcast' => 'پادکست',
            'question' => 'سؤال'
        ]
    ],
    'faq' => [
        'types' => [
            'rule' => 'قانون',
            'guild' => 'گروه'
        ]
    ],
    'podcast' => [
        'status' => [
            'published' => 'منتشر شده',
            'draft' => 'پیش‌نویس',
            'coming_soon' => 'به زودی',
        ],
        'type' => [
            'serial' => 'سریال',
            'single' => 'تکی',
        ],
        'content' => [
            'sound' => 'صدا',
            'video' => 'ویدیو'
        ]
    ],
    'question' => [
        'status' => [
            'published' => 'منتشر شده',
            'draft' => 'پیش‌نویس',
            'coming_soon' => 'به زودی',
        ],
        'difficulty' => [
            'easy' => 'آسان',
            'normal' => 'عادی',
            'hard' => 'سخت',
        ]
    ],
    'subscription' => [
        'status' => [
            'published' => 'منتشر شده',
            'draft' => 'پیش‌نویس',
        ],
        'discount' => [
            'const' => 'ثابت',
            'percent' => 'درصد'
        ]
    ],
    'invoice' => [
        'status' => [
            'pending' => 'در انتظار',
            'error' => 'خطا',
            'paid' => 'پرداخت شده',
        ],
    ],
    'comment' => [
        'status' => [
            'published' => 'منتشر شده',
            'draft' => 'پیش‌نویس'
        ],
    ],
    'home' => [
        'elements' => [
            'slider' => 'نمایش اسلایدر',
            'grid' => 'نمایش شبکه‌ای',
            'single' => 'نمایش مدل تکی'
        ],
        'content' => [
            'category' => 'دسته‌بندی',
            'product' => 'محصول',
            'podcast' => 'پادکست',
            'banner' => 'بنر',
            'with_latest' => 'با جدیدترین‌ها',
            'with_oldest' => 'با قدیمی‌ترین‌ها',
            'with_popular' => 'با محبوب‌ترین‌ها',
        ],
        'type' => [
            'single_tab' => 'تک تب',
            'multiple_tab' => 'چند تب'
        ]
    ],
    'parameters' => [
        'name' => 'نام کاربر',
        'subscription' => 'عنوان اشتراک',
        'amount' => 'مبلغ'
    ],
    'checkout' => [
        'pending' => 'در انتظار',
        'done' => 'انجام شده',
        'failed' => 'ناموفق'
    ],
    'task' => [
        'types' => [
            'user' => 'کاربر',
            'team' => 'تیم'
        ]
    ],
    'reward' => [
        'types' => [
            'user' => 'کاربر',
            'team' => 'تیم'
        ]
    ]
];
