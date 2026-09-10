<?php

namespace Amranidev\Laracombee\Console;

use Amranidev\Laracombee\Facades\LaracombeeFacade as Laracombee;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Share model validation, request helpers, and exit-code handling between commands.
 */
class LaracombeeCommand extends Command
{
    /**
     * Add User property.
     *
     * @param string $property
     * @param string $type
     *
     * @return \Recombee\RecommApi\Requests\AddUserProperty
     */
    public function addUserProperty(string $property, string $type)
    {
        return Laracombee::addUserProperty($property, $type);
    }

    /**
     * Add Item property.
     *
     * @param string $property
     * @param string $type
     *
     * @return \Recombee\RecommApi\Requests\AddItemProperty
     */
    public function addItemProperty(string $property, string $type)
    {
        return Laracombee::addItemProperty($property, $type);
    }

    /**
     * Delete User property.
     *
     * @param string $property
     *
     * @return \Recombee\RecommApi\Requests\DeleteUserProperty
     */
    public function deleteUserProperty(string $property)
    {
        return Laracombee::deleteUserProperty($property);
    }

    /**
     * Delete Item property.
     *
     * @param string $property
     *
     * @return \Recombee\RecommApi\Requests\DeleteItemProperty
     */
    public function deleteItemProperty(string $property)
    {
        return Laracombee::deleteItemProperty($property);
    }

    /**
     * Build Recombee value-setting requests from the supplied model.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Recombee\RecommApi\Requests\SetUserValues
     */
    public function addUser(Authenticatable $user)
    {
        return Laracombee::addUser($user);
    }

    /**
     * Build Recombee value-setting requests from the supplied model.
     *
     * @param \Illuminate\Database\Eloquent\Model $item
     * @return \Recombee\RecommApi\Requests\SetItemValues
     */
    public function addItem(Model $item)
    {
        return Laracombee::addItem($item);
    }

    /**
     * Build Recombee value-setting requests from the supplied models.
     *
     * @param array<\Illuminate\Contracts\Auth\Authenticatable> $batch
     * @return array<\Recombee\RecommApi\Requests\SetUserValues>
     */
    public function addUsers(array $batch)
    {
        return Laracombee::addUsers($batch);
    }

    /**
     * Build Recombee value-setting requests from the supplied models.
     *
     * @param array<\Illuminate\Database\Eloquent\Model> $batch
     * @return array<\Recombee\RecommApi\Requests\SetItemValues>
     */
    public function addItems(array $batch)
    {
        return Laracombee::addItems($batch);
    }
    /**
     * Run a command operation and convert exceptions into a failure exit code.
     *
     * @param callable(): void $operation
     * @return int
     */
    protected function executeOperation(callable $operation): int
    {
        try {
            $operation();
            $this->info('Done!');

            return self::SUCCESS;
        } catch (\Throwable $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Validate a catalog selector before constructing requests.
     *
     * @param string|null $type
     * @return string
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    protected function catalogType(?string $type): string
    {
        if (!in_array($type, ['user', 'item'], true)) {
            throw new \InvalidArgumentException('Catalog type must be user or item.');
        }

        return $type;
    }

    /**
     * Resolve and validate the configured Eloquent model for a catalog.
     *
     * @param string $type
     * @return class-string<\Illuminate\Database\Eloquent\Model>
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    protected function modelClass(string $type): string
    {
        $class = config('laracombee.'.$this->catalogType($type));

        if (!is_string($class) || !is_subclass_of($class, Model::class)) {
            throw new \InvalidArgumentException('Configure an Eloquent model for laracombee.'.$type.'.');
        }

        if ($type === 'user' && !is_subclass_of($class, Authenticatable::class)) {
            throw new \InvalidArgumentException('The user model must implement Authenticatable.');
        }

        return $class;
    }

    /**
     * Resolve the configured mapper’s schema definitions for a catalog.
     *
     * @param string $type
     * @return array<string, string>
     *
     * @throws \InvalidArgumentException When the model or supplied arguments are invalid.
     */
    protected function modelProperties(string $type): array
    {
        return app(\Amranidev\Laracombee\ModelMapper::class)->properties($this->modelClass($type));
    }
}
