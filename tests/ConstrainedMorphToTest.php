<?php

/* @noinspection PhpUnused, PhpArgumentWithoutNamedIdentifierInspection, PhpIllegalPsrClassPathInspection, PhpUndefinedMethodInspection */

declare(strict_types=1);

namespace Pindab0ter\ConstrainedMorphToForLaravel;

use Illuminate\Database\Eloquent\Collection;
use Workbench\App\Models\Comment;
use Workbench\App\Models\Post;
use Workbench\App\Models\Video;

it('returns related model when constraint matches', function () {
    $post = Post::create();

    $comment = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);

    expect($comment->post)->toBeInstanceOf(Post::class);
});

it('returns null when constraint does not match', function () {
    $video = Video::create();

    $comment = Comment::create([
        'commentable_id' => $video->id,
        'commentable_type' => Video::class,
    ]);

    expect($comment->post)->toBeNull();
});

it('returns correct models when lazy loading', function () {
    $video = Video::create();
    $post = Post::create();

    $comment1 = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);

    $comment2 = Comment::create([
        'commentable_id' => $video->id,
        'commentable_type' => Video::class,
    ]);

    $comments = Comment::all()->load('post');

    expect($comments->firstWhere('id', $comment1->id)?->post)
        ->toBeInstanceOf(Post::class)
        ->and($comments->firstWhere('id', $comment2->id)?->post)
        ->toBeNull();
});

it('returns correct models when eager loading', function () {
    $video = Video::create();
    $post = Post::create();

    $comment1 = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);

    $comment2 = Comment::create([
        'commentable_id' => $video->id,
        'commentable_type' => Video::class,
    ]);

    /** @var Collection<int, Comment> $comments */
    $comments = Comment::with('post')->get();

    expect($comments->firstWhere('id', $comment1->id)?->post)
        ->toBeInstanceOf(Post::class)
        ->and($comments->firstWhere('id', $comment2->id)?->post)
        ->toBeNull();
});

it('returns null when type is empty', function () {
    $comment = Comment::create();

    expect($comment->post)->toBeNull();
});

it('returns related model when one of multiple allowed types matches during lazy loading', function () {
    $post = Post::create();
    $video = Video::create();

    $comment1 = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);

    $comment2 = Comment::create([
        'commentable_id' => $video->id,
        'commentable_type' => Video::class,
    ]);

    expect($comment1->commentable)
        ->toBeInstanceOf(Post::class)
        ->and($comment2->commentable)
        ->toBeInstanceOf(Video::class);
});

it('returns correct models when eager loading with multiple allowed types', function () {
    $video = Video::create();
    $post = Post::create();

    $comment1 = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);

    $comment2 = Comment::create([
        'commentable_id' => $video->id,
        'commentable_type' => Video::class,
    ]);

    /** @var Collection<int, Comment> $comments */
    $comments = Comment::with('commentable')->get();

    expect($comments->firstWhere('id', $comment1->id)?->commentable)
        ->toBeInstanceOf(Post::class)
        ->and($comments->firstWhere('id', $comment2->id)?->commentable)
        ->toBeInstanceOf(Video::class);
});
