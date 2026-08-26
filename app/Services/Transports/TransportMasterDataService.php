<?php

namespace App\Services\Transports;

use App\Models\TransportBrand;
use App\Models\TransportType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class TransportMasterDataService
{
    public const TYPE = 'type';
    public const BRAND = 'brand';

    public function definition(string $resource): array
    {
        return match ($resource) {
            self::TYPE => [
                'title' => 'Transport Types',
                'singular' => 'Transport Type',
                'field' => 'type',
                'model' => TransportType::class,
                'route' => 'admin.transport-types',
                'parameter' => 'transportType',
                'relation' => 'transports',
            ],
            self::BRAND => [
                'title' => 'Transport Brands',
                'singular' => 'Transport Brand',
                'field' => 'brand',
                'model' => TransportBrand::class,
                'route' => 'admin.transport-brands',
                'parameter' => 'transportBrand',
                'relation' => 'transports',
            ],
            default => throw new \InvalidArgumentException('Unsupported Transport master data resource.'),
        };
    }

    public function index(string $resource, ?string $search = null): LengthAwarePaginator
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];
        $field = $definition['field'];

        return $model::query()
            ->withCount($definition['relation'])
            ->when($search, fn ($query) => $query->where($field, 'like', '%' . $search . '%'))
            ->orderBy($field)
            ->paginate(20)
            ->withQueryString();
    }

    public function store(string $resource, string $value): Model
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        return $model::create([$definition['field'] => $value]);
    }

    public function update(string $resource, Model $masterData, string $value): Model
    {
        $definition = $this->definition($resource);

        if ($this->value($masterData, $resource) !== $value && $this->usageCount($resource, $masterData) > 0) {
            throw new \LogicException('This master data is still used by Transport records and cannot be renamed.');
        }

        $masterData->update([$definition['field'] => $value]);

        return $masterData->refresh();
    }

    public function usageCount(string $resource, Model $masterData): int
    {
        return (int) $masterData->transports()->count();
    }

    public function delete(string $resource, Model $masterData): void
    {
        if ($this->usageCount($resource, $masterData) > 0) {
            throw new \LogicException('This master data is still used by Transport records.');
        }

        $masterData->delete();
    }

    public function value(Model $masterData, string $resource): string
    {
        return (string) $masterData->{$this->definition($resource)['field']};
    }
}
