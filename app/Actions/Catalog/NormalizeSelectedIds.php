<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Collection;

class NormalizeSelectedIds
{
    /**
     * @param  array<int|string, mixed>  $grouped
     * @return Collection<int, int>
     */
    public static function forKey(array $grouped, int|string $key): Collection
    {
        if (array_key_exists($key, $grouped)) {
            return self::from($grouped[$key]);
        }

        $stringKey = (string) $key;

        if (array_key_exists($stringKey, $grouped)) {
            return self::from($grouped[$stringKey]);
        }

        $intKey = (int) $key;

        if ((string) $intKey === $stringKey && array_key_exists($intKey, $grouped)) {
            return self::from($grouped[$intKey]);
        }

        return collect();
    }

    /**
     * @return Collection<int, int>
     */
    public static function from(mixed $raw): Collection
    {
        if ($raw === null || $raw === false || $raw === '') {
            return collect();
        }

        if (! is_array($raw)) {
            $raw = [$raw];
        }

        return collect($raw)
            ->flatMap(fn (mixed $value, mixed $key): array => self::idsFromEntry($value, $key))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();
    }

    /**
     * @return list<int>
     */
    private static function idsFromEntry(mixed $value, mixed $key): array
    {
        if (is_array($value)) {
            return self::from($value)->all();
        }

        if (is_bool($value) || $value === 'true' || $value === 'on') {
            return $value === false ? [] : [(int) $key];
        }

        if ($value === 'false' || $value === 'off' || $value === null || $value === '') {
            return [];
        }

        return [(int) $value];
    }
}
