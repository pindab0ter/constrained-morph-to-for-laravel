<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\Relation;
use Pindab0ter\ConstrainedMorphToForLaravel\Tests\TestCase;

uses(TestCase::class)
    ->afterEach(function () {
        Relation::morphMap([], merge: false);
        Relation::requireMorphMap(false);
    })
    ->in(__DIR__);
