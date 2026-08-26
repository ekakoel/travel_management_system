<?php

namespace App\Services\Activities;

use App\Models\ActivityType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class ActivityMasterDataService
{
    public const TYPE = 'type';
    public const BRAND = 'brand';

    public function definition(string $resource): array
    {
        return match ($resource) {
            self::TYPE => [
                'title' => 'Activity Types',
                'singular' => 'Activity Type',
                'field' => 'type',
                'model' => ActivityType::class,
                'route' => 'admin.activity-types',
                'parameter' => 'activityType',
                'relation' => 'activities',
            ],
            default => throw new \InvalidArgumentException('Unsupported Activity master data resource.'),
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
            throw new \LogicException('This master data is still used by Activity records and cannot be renamed.');
        }

        $masterData->update([$definition['field'] => $value]);

        return $masterData->refresh();
    }

    public function usageCount(string $resource, Model $masterData): int
    {
        return (int) $masterData->activities()->count();
    }

    public function delete(string $resource, Model $masterData): void
    {
        if ($this->usageCount($resource, $masterData) > 0) {
            throw new \LogicException('This master data is still used by Activity records.');
        }

        $masterData->delete();
    }

    public function value(Model $masterData, string $resource): string
    {
        return (string) $masterData->{$this->definition($resource)['field']};
    }
}
