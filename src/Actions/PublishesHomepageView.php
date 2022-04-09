<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Hyde;

class PublishesHomepageView implements ActionContract
{
    public static array $homePages = [
        'welcome' => [
            'name' => 'Welcome',
            'path' => 'resources/views/homepages/welcome.blade.php',
            'description' => 'The default welcome page.',
        ],
        'posts' => [
            'name' => 'Posts Feed',
            'path' => 'resources/views/homepages/post-feed.blade.php',
            'description' => 'A feed of your latest posts. Perfect for a blog site!',
        ],
        'blank' => [
            'name' => 'Blank Starter',
            'path' => 'resources/views/homepages/blank.blade.php',
            'description' => 'A blank Blade template with just the base layout.',
        ],
    ];

    protected string $selected;
    protected bool $force = false;

    public function __construct(string $selected, bool $force = false)
    {
        $this->force = $force;
        $this->selected = $selected;
    }

    public function execute(): bool|int
    {
        if (! array_key_exists($this->selected, self::$homePages)) {
            return 404;
        }

        return Hyde::copy(
            Hyde::vendorPath(static::$homePages[$this->selected]['path']),
            Hyde::path('_pages/index.blade.php'),
            $this->force
        );
    }
}
