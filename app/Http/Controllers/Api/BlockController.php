<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BlockRepository;
use App\Http\Resources\BlockResource;

class BlockController extends Controller
{
    protected BlockRepository $repo;

    // Репозиторий внедряется через DI
    public function __construct(BlockRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(string $slug)
    {
        return new BlockResource(
            $this->repo->getBlock($slug)
        );
    }

}
