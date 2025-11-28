<?php

declare(strict_types=1);

if (function_exists('arch')) {
    arch('it does not use debugging functions')
        ->expect(['dd', 'dump', 'ray'])
        ->each->not->toBeUsed();
}
