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
    /** @var array<array-key, class-string<TRelatedModel>> */
    protected readonly array $allowedTypes;

    /**
     * @param  Builder<TRelatedModel>  $query
     * @param  TDeclaringModel  $parent
     * @param  string  $foreignKey
     * @param  string|null  $ownerKey
     * @param  string  $type
     * @param  string  $relation
     * @param  class-string<TRelatedModel>|array<array-key, class-string<TRelatedModel>>  $constrainedTo
     */
    public function __construct(Builder $query, $parent, $foreignKey, $ownerKey, $type, $relation, string|array $constrainedTo)
    {
        $this->allowedTypes = is_array($constrainedTo) ? $constrainedTo : [$constrainedTo];

        parent::__construct($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }

    private function isAllowedType(string $type): bool
    {
        return in_array($type, $this->allowedTypes, strict: true);
    }

    protected function buildDictionary(EloquentCollection $models): void
    {
        /** @var EloquentCollection<array-key, TRelatedModel> $filteredModels */
        $filteredModels = $models->filter(fn (Model $model) => $this->isAllowedType($model->{$this->morphType}));

        parent::buildDictionary($filteredModels);
    }

    /** @return TRelatedModel|null */
    public function getResults(): ?Model
    {
        $type = $this->parent->{$this->morphType};

        if ($type === null || ! $this->isAllowedType($type)) {
            return null;
        }

        return parent::getResults();
    }
}
