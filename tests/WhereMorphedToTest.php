<?php

declare(strict_types=1);

namespace Pindab0ter\ConstrainedMorphToForLaravel;

use Workbench\App\Models\Comment;
use Workbench\App\Models\Post;
use Workbench\App\Models\Video;

it('qualifies the morph columns against the parent table', function () {
    $post = Post::create();
    $comment = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);
    Comment::create([
        'commentable_id' => Video::create()->id,
        'commentable_type' => Video::class,
    ]);

    expect(Comment::whereMorphedTo('commentable', $post)->pluck('id')->all())
        ->toBe([$comment->id]);
});

it('qualifies the morph type column against the parent table when given a class name', function () {
    Comment::create([
        'commentable_id' => Post::create()->id,
        'commentable_type' => Post::class,
    ]);
    $videoComment = Comment::create([
        'commentable_id' => Video::create()->id,
        'commentable_type' => Video::class,
    ]);

    expect(Comment::whereMorphedTo('commentable', Video::class)->pluck('id')->all())
        ->toBe([$videoComment->id]);
});
