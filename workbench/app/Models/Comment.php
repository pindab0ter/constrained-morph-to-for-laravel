<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Pindab0ter\ConstrainedMorphToForLaravel\ConstrainedMorphTo;
use Pindab0ter\ConstrainedMorphToForLaravel\HasConstrainedMorphTo;

/**
 * @property int $id
 * @property ?int $commentable_id
 * @property ?class-string<Post> $commentable_type
 * @property ?Post $video
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Comment extends Model
{
    use HasConstrainedMorphTo;
    use HasTimestamps;

    protected $fillable = [
        'commentable_id',
        'commentable_type',
    ];

    /** @return ConstrainedMorphTo<Post, $this> */
    public function post(): ConstrainedMorphTo
    {
        return $this->constrainedMorphTo(Post::class, 'commentable_type', 'commentable_id');
    }

    /** @return ConstrainedMorphTo<Post|Video, $this> */
    public function commentable(): ConstrainedMorphTo
    {
        return $this->constrainedMorphTo(
            [Post::class, Video::class],
            'commentable_type',
            'commentable_id'
        );
    }
}
