<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasParent
{

    protected $appendsParentFields = [
        'full_path',
    ];


    public $all_children = [];

    public $tree_children = [];


    public function initHasParent()
    {
        $this->appends = array_merge($this->appends, $this->appendsParentFields);
    }


    abstract public function parent();

    abstract public function children();


    public function getFullPath($item = null)
    {
        if (!$item) {
            $item = $this;
        }

        if (!$parent = $item->parent) {
            return Str::start($item->slug, '/');
        }

        return Str::start(sprintf('%s%s', $this->getFullPath($parent), Str::start($item->slug, '/')), '/');
    }

    public function getFullName($item = null)
    {
        if (!$item) {
            $item = $this;
        }

        if (!$parent = $item->parent) {
            return $item->name;
        }

        return $this->getFullName($parent) . ' / ' . $item->name;
    }


    public function getAllChildren($item = null)
    {
        if (!$item) {
            $item = $this;
        }

        if (!$this->all_children) {
            $this->all_children = collect();
            $item->full_name = $item->getFullName($item);
            $this->all_children->add($item);
        }

        if (isset($item->children)) {
            foreach ($item->children as $children) {
                $children->full_name = $this->getFullName($children);
                $this->all_children->add($children);
                $this->getAllChildren($children);
            }

        } else {
            $this->all_children->add($item);
        }

        return $this->all_children;

    }

    /*
        public function getTree($item = null)
        {
            if (!$item) {
                $item = $this;
            }


            if (!$this->tree_children) $this->tree_children = [];

            $this->tree_children[$item->key]=$item;

            if (isset($item->children)) {

                $this->tree_children[$item->key]=$item;
                return $this->getAllChildren($item->children);
            }

            return $this->all_children;

        }*/



    public function getRootSlug()
    {
        $pathSections = explode('/', $this->getFullPath());

        return collect($pathSections)->filter()->first();
    }


    public function getRootParentAttribute()
    {
        if (!$this->parent) {
            return null;
        }

        return self::whereSlug($this->getRootSlug())->first();
    }


    public function getFullPathAttribute()
    {
        $pathSections = explode('/', $this->getFullPath());

        $slugsToExclude = Arr::wrap($this->domainMappedFolders);

        $finalSlug = collect($pathSections)
            ->filter()
            ->reject(fn($slug) => in_array($slug, $slugsToExclude))
            ->implode('/');

        return Str::start($finalSlug, '/');
    }


    public function scopeWithParent(Builder $query)
    {
        return $query->with('parent');
    }


    public function scopeWithChildren(Builder $query)
    {
        return $query->with('children');
    }


    public function scopeWhereFullPath(Builder $query, string $path)
    {
        $itemSlugs = explode('/', $path);

        return $query->where('slug', '=', end($itemSlugs))
            ->get()
            ->filter(fn($item) => $item->full_path === str_start($path, '/'));
    }
}
