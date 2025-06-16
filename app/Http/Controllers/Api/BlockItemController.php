<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BlockItemRepository;
use App\Http\Resources\BlockItemResource;

class BlockItemController extends Controller
{
    protected BlockItemRepository $repo;

    // Репозиторий внедряется через DI
    public function __construct(BlockItemRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(string $slug)
    {
        return new BlockItemResource(
            $this->repo->getItem($slug)
        );
    }

}
