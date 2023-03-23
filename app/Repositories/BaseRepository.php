<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;

abstract class BaseRepository
{
    /** @var Model */
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModelInstance();
    }

    /**
     * Retorna uma nova instância do modelo correto para este repositório.
     */
    protected function getModelInstance()
    {
        // Obtém a anotação @var do modelo a partir da docblock da classe
        $docblock = (new ReflectionClass(get_called_class()))
            ->getProperty('model')
            ->getDocComment();
            
        $type = str_replace(['@var', '/**', '*/'], '', $docblock);
        $model = "App\\Models\\" . trim($type);

        // Cria uma nova instância do modelo correto
        return new $model();
    }

    public function whereModel(string $id)
    {
        $this->model = $this->model->find($id);
        return $this;
    }

    public function whereIn(array $ids): ?array
    {
        return $this->model->whereIn('id', $ids)
            ?->get()
            ?->toArray();
    }

    public function get(): ?array
    {
        return $this->model?->toArray();
    }

    public function updateOrCreate(array $data)
    {
        $this->model = $this->model->updateOrCreate(
            ['id' => $this->model?->id],
            $data
        );
    }

    public function create(array $data): array
    {
        $model = $this->model->create($data);

        return $model->toArray();
    }

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }

    public function delete(): ?bool
    {
        return $this->model->delete();
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function find(string $id): array
    {
        $model = $this->model->findOrFail($id);

        return $model->toArray();
    }

    /**
     * @param string $field
     * @param int|float|string $value
     *
     * @return ?array
     */
    public function findBy(string $field, int|float|string $value): ?array
    {
        return $this->model
            ->query()
            ->firstWhere($field, '=', $value)
            ?->toArray();
    }

    public function all(): array
    {
        return $this->model->all()->toArray();
    }

    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return Collection
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): Collection
    {
        $paginator = $this->model->paginate($perPage, $columns);

        $items = $paginator->getCollection()->map(function ($model) {
            return $model->toArray();
        });

        return new Collection($items, $paginator->currentPage(), $paginator->perPage(), $paginator->total());
    }
}
