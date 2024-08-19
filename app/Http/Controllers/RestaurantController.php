<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
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
        //
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
        //
    }
}
