<?php

declare(strict_types=1);

namespace Pindab0ter\ConstrainedMorphToForLaravel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @template TRelatedModel of Model
 * @template TDeclaringModel of Model
 *
 * @extends MorphTo<TRelatedModel, TDeclaringModel>
 */
class ConstrainedMorphTo extends MorphTo
{
    /** @var class-string<TRelatedModel> */
    protected readonly string $allowedType;

    /**
     * @param  Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string|null  $ownerKey
     * @param  string  $type
     * @param  string  $relation
     * @param  class-string<TRelatedModel>  $constrainedTo
     */
    public function __construct(Builder $query, $parent, $foreignKey, $ownerKey, $type, $relation, string $constrainedTo)
    {
        $this->allowedType = $constrainedTo;

        parent::__construct($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }

    protected function buildDictionary(EloquentCollection $models): void
    {
        /** @var EloquentCollection<array-key, TRelatedModel> $filteredModels */
        $filteredModels = $models->filter(fn (Model $model) => $model->{$this->morphType} === $this->allowedType);

        parent::buildDictionary($filteredModels);
    }

    /** @return TRelatedModel|null */
    public function getResults(): ?Model
    {
        if ($this->parent->{$this->morphType} !== $this->allowedType) {
            return null;
        }

        return parent::getResults();
    }
}
