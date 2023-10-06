<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBrandRequest;
use App\Http\Services\BrandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class BrandController extends Controller
{
    function __construct(
        protected BrandService $brandService
    ) {
    }

    function index(Request $request): mixed
    {
        return $this->brandService->index($request);        
    }

    function create(): Response
    {
        return $this->brandService->create();
    }

    function store(AddBrandRequest $request): RedirectResponse
    {
        return $this->brandService->store($request);        
    }

    function show(): mixed
    {
    }

    function update(): mixed
    {
    }

    function destroy(Request $request): mixed
    {
        return $this->brandService->destroy($request);
    }
}
