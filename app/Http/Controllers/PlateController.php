<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Requests\StorePlateRequest;
use App\Http\Requests\UpdatePlateRequest;
use App\Http\Resources\PaginatedListCollection;
use App\Http\Resources\PlateResource;
use App\Models\Plate;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class PlateController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Restaurant $restaurant) {
        Gate::authorize('viewPlates', $restaurant);
        $plates = $restaurant->plates()->paginate();
        return ApiResponseHelper::successResponse(data: new PaginatedListCollection($plates), message: "Plates retrieved successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlateRequest $request, Restaurant $restaurant) {
        Gate::authorize('view', $restaurant);
        $plate = $restaurant->plates()->create($request->validated());
        return ApiResponseHelper::successResponse(data: PlateResource::make($plate), message: "Plate created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant, Plate $plate) {
        Gate::authorize('view', $restaurant);
        return ApiResponseHelper::successResponse(data: PLateResource::make($plate), message: "Plate retrieved successfully");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlateRequest $request, Restaurant $restaurant, Plate $plate) {
        Gate::authorize('view', $restaurant);
        $plate->update($request->validated());
        return ApiResponseHelper::successResponse(data: PlateResource::make($plate->refresh()), message: "Plate created successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant, Plate $plate) {
        Gate::authorize('view', $restaurant);
        $plate->delete();
        return ApiResponseHelper::successResponse();
    }
}
