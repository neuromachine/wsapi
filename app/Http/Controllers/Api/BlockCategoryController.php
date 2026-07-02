<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BlockCategoryRepository;
use App\Http\Resources\BlockCategoryResource;
use App\Http\Resources\BlockCategoryStructureResource;
use App\Http\Resources\OffersResource;

class BlockCategoryController extends Controller
{
    protected BlockCategoryRepository $repo;

    // Репозиторий внедряется через DI
    public function __construct(BlockCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(string $locale, string $slug): BlockCategoryResource
    {
        return new BlockCategoryResource(
            $this->repo->getCategory($locale,$slug)
        );
    }

    public function structure(string $locale,?string $slug = null)
    {
        return new BlockCategoryStructureResource(
            $this->repo->getCategoriesRecursive($locale,$slug)
        );
    }

    public function offers(string $locale,string $slug)
    {
        return response()->json(
            new OffersResource($this->repo->getOffersData($locale, $slug))
        );
    }
}
