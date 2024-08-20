<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\PaginatedListCollection;
use App\Http\Resources\RestaurantCollection;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $restaurants = auth()->user()->restaurants()->paginate();
        return ApiResponseHelper::successResponse(data: new PaginatedListCollection($restaurants), message: "Restaurants retrieved successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantRequest $request) {
        $restaurant = auth()->user()->restaurants()->create($request->validated());
        return ApiResponseHelper::successResponse(data: RestaurantResource::make($restaurant),);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant) {
        Gate::authorize('view', $restaurant);
        return ApiResponseHelper::successResponse(data: RestaurantResource::make($restaurant),);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant) {
        Gate::authorize('update', $restaurant);
        $restaurant->update($request->validated());
        return ApiResponseHelper::successResponse(data: RestaurantResource::make($restaurant));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Restaurant $restaurant) {
        Gate::authorize('delete', $restaurant);
        $restaurant->delete();
        return ApiResponseHelper::successResponse();
    }
}
