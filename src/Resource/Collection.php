<?php

namespace Flagrow\Flarum\Api\Resource;

use Flagrow\Flarum\Api\Cache;
use Flagrow\Flarum\Api\Flarum;
use Illuminate\Support\Collection as Collect;

class Collection extends Resource
{
    /**
     * @var array|Item[]
     */
    protected $items = [];

    /**
     * Collection constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $item) {
            $item = new Item($item);
            $this->items[$item->id] = $item;
        }
    }

    /**
     * @return Collection
     */
    public function cache()
    {
        foreach ($this->items as $id => $item) {
            $item->cache();
        }

        return $this;
    }

    /**
     * @return Collect
     */
    public function collect(): Collect
    {
        return collect($this->items)->keyBy('id');
    }

    /**
     * @param string $by
     * @param int|null $amount
     * @return Collect
     */
    public function latest(string $by = 'created_at', int $amount = null): Collect
    {
        $set = $this->collect()->sortBy($by);

        if ($amount) {
            $set = $set->splice(0, $amount);
        }

        return $set;
    }

    /**
     * Merge another Collection into this one
     * @param Collection $items
     * @return Collection
     */
    public function merge(Collection $items)
    {
        $this->items = $this->collect()->merge($items->collect())->keyBy('id');

        return $this;
    }
}
