<?php

namespace Amranidev\Laracombee;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Resolve model identifiers, declared schema properties, and exportable values.
 */
class ModelMapper
{
    /**
     * Resolve a model identifier, honoring custom Eloquent primary keys.
     *
     * @param object $model
     * @return string|int
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    public function identifier(object $model): string|int
    {
        $id = $model instanceof Model ? $model->getKey()
            : ($model instanceof Authenticatable ? $model->getAuthIdentifier() : null);

        if (!is_string($id) && !is_int($id)) {
            throw new InvalidArgumentException('The Recombee model must have a persisted string or integer identifier.');
        }

        return $id;
    }

    /**
     * Validate and return the model’s public static Recombee property definitions.
     *
     * @param class-string $model
     * @return array<string, string>
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    public function properties(string $model): array
    {
        $properties = get_class_vars($model)['laracombee'] ?? null;

        if (!is_array($properties) || !(new \ReflectionProperty($model, 'laracombee'))->isStatic()) {
            throw new InvalidArgumentException($model.' must define a public static $laracombee property array.');
        }

        foreach ($properties as $name => $type) {
            if (!is_string($name) || $name === '' || !is_string($type) || $type === '') {
                throw new InvalidArgumentException('Recombee properties must map property names to type strings.');
            }
        }

        return $properties;
    }

    /**
     * Export declared model properties while respecting model serialization visibility.
     *
     * @param object $model
     * @return array<string, mixed>
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    public function values(object $model): array
    {
        if (!is_callable([$model, 'toArray'])) {
            throw new InvalidArgumentException('The Recombee model must implement toArray(), or use a custom ModelMapper.');
        }

        return array_intersect_key($model->toArray(), $this->properties($model::class));
    }
}
