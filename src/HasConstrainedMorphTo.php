<?php

declare(strict_types=1);

namespace Pindab0ter\ConstrainedMorphToForLaravel;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasConstrainedMorphTo
{
    /**
     * Define a polymorphic, inverse one-to-one or many relationship, which only allows a specific type of model to be related.
     *
     * @template TRelatedModel of Model
     *
     * @param  class-string<TRelatedModel>  $constrainedTo
     * @return ConstrainedMorphTo<TRelatedModel, $this>
     */
    public function constrainedMorphTo(string $constrainedTo, string $type, string $id, ?string $name = null, ?string $ownerKey = null): ConstrainedMorphTo
    {
        // If no name is provided, the backtrace will be used to get the function name
        // since that is most likely the name of the polymorphic interface.
        $name = $name ?: $this->guessBelongsToRelation();

        // If the type value is null, it is probably safe to assume the relationship is being eagerly loading
        // the relationship. In this case we will create a query from the constrained type since we know
        // what type is allowed, and we need to remove any eager loads that may already be defined on a model.
        $class = $this->getAttributeFromArray($type);

        if (empty($class)) {
            // Use the constrained type to create a properly typed query builder
            /** @var Builder<TRelatedModel> $query */
            $query = $this->newRelatedInstance($constrainedTo)->newQuery()->setEagerLoads([]);
        } else {
            // Assert that the morph class matches our template type TRelatedModel
            /** @phpstan-var class-string<TRelatedModel> $morphClass */
            $morphClass = static::getActualClassNameForMorph($class);

            $instance = $this->newRelatedInstance($morphClass);
            /** @var Builder<TRelatedModel> $query */
            $query = $instance->newQuery()->setEagerLoads([]);
            $ownerKey ??= $instance->getKeyName();
        }

        return new ConstrainedMorphTo(
            query: $query,
            parent: $this,
            foreignKey: $id,
            ownerKey: $ownerKey,
            type: $type,
            relation: $name,
            constrainedTo: $constrainedTo,
        );
    }
}
