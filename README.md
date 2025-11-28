# Constrained MorphTo for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pindab0ter/constrained-morph-to-for-laravel.svg?style=flat-square)](https://packagist.org/packages/pindab0ter/constrained-morph-to-for-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pindab0ter/constrained-morph-to-for-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pindab0ter/constrained-morph-to-for-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pindab0ter/constrained-morph-to-for-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pindab0ter/constrained-morph-to-for-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pindab0ter/constrained-morph-to-for-laravel.svg?style=flat-square)](https://packagist.org/packages/pindab0ter/constrained-morph-to-for-laravel)

This package provides type-constrained polymorphic relationships for Laravel Eloquent. It extends Laravel’s `morphTo` relationship to only accept specific model types, ensuring data integrity and type safety for your polymorphic relations.

## The Problem

Laravel’s polymorphic relationships are flexible - a single relationship can relate to multiple model types. But sometimes you want the flexibility of a polymorphic database structure while enforcing that certain relationships only accept specific types:

```php
// Standard Laravel morphTo - returns ANY model type
public function commentable()
{
    return $this->morphTo();
}

$comment->commentable; // Returns any model type
```

## The Solution

This package lets you constrain polymorphic relationships to specific types:

```php
use pindab0ter\ConstrainedMorphtoForLaravel\HasConstrainedMorphTo;

class Comment extends Model
{
    use HasConstrainedMorphTo;

    /** @return ConstrainedMorphTo<Post, $this> */
    public function post()
    {
        return $this->constrainedMorphTo(Post::class, 'commentable_type', 'commentable_id');
    }
}

$comment->post; // Returns a Post if the type matches, null if it doesn't
```

## Requirements

- PHP 8.2+
- Laravel 10.48+, 11.x, or 12.x

## Installation

Install the package via Composer:

```bash
composer require pindab0ter/constrained-morph-to-for-laravel
```

## Usage

### Basic Usage

1. Add the `HasConstrainedMorphTo` trait to your model:

    ```php
    use Illuminate\Database\Eloquent\Model;
    use pindab0ter\ConstrainedMorphtoForLaravel\HasConstrainedMorphTo;

    class Comment extends Model
    {
        use HasConstrainedMorphTo;

        /** @return ConstrainedMorphTo<Post, $this> */
        public function commentable()
        {
            return $this->constrainedMorphTo(
                Post::class,        // Only allow Post models
                'commentable_type', // The type column name
                'commentable_id'    // The ID column name
            );
        }
    }
    ```

2. When the relationship type matches, it works like normal `morphTo`:

    ```php
    $post = Post::create([...]);
    $comment = Comment::create([
        'commentable_id' => $post->id,
        'commentable_type' => Post::class,
    ]);

    $comment->commentable; // Returns the Post instance
    ```

3. When the type doesn't match the constraint, it returns `null`:

    ```php
    $image = Image::create([...]);
    $comment = Comment::create([
        'commentable_id' => $image->id,
        'commentable_type' => Image::class, // Wrong type!
    ]);

    $comment->commentable; // Returns null (constraint not met)
    ```

### Advanced Usage

You can optionally specify the relationship name and owner key:

```php
public function commentable()
{
    return $this->constrainedMorphTo(
        Post::class,        // Constrained type
        'commentable_type', // Type column
        'commentable_id',   // ID column
        'commentable',      // Relationship name (optional)
        'id'                // Owner key (optional)
    );
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
