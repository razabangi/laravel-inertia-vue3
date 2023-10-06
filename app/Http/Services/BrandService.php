<?php

namespace App\Http\Services;

use App\Http\Requests\AddBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BrandService {
    function index(Request $request) : mixed {
        $query = Brand::query();
        $request->validate([
            'order_by' => 'in:id,name',
            'direction' => 'in:asc,desc'
        ]);
        if ($request->has(['order_by', 'direction'])) {
            $query->orderBy($request->order_by, $request->direction);
        }

        if ($request->has('search')) {
            $query->where('id', $request->search ?? '')->orWhere('name', 'LIKE', "%$request->search%");
        }

        $brands = $query->latest()->paginate(5);
        return Inertia::render("Admin/Brand/List", [
            "brands" => BrandResource::collection($brands),
            'filters' => $request->all(['order_by', 'direction']),
        ]);
    }

    function create() : Response {
        return Inertia::render("Admin/Brand/Create");
    }

    function store(AddBrandRequest $request) : RedirectResponse {
        Brand::create($request->all());
        return to_route('admin.brand.index')->with('message', 'Brand has been added successfully.');
    }

    function destroy(Request $request) : mixed {
        $brand = Brand::find($request->id);

        if ($brand) {
            if ($brand->delete()) {
                return response()->json([
                    'success' => true
                ], 200);
            }
        }
    }
}