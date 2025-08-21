<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CateringPackageApiResource;
use App\Models\CateringPackage;
use Illuminate\Http\Request;

class CateringPackageController extends Controller
{
    //
    public function index()
    {
        $cateringPackages = CateringPackage::with(['city', 'kitchen', 'category', 'tiers', 'tiers.benefits'])->get();
        return CateringPackageApiResource::collection($cateringPackages);
    }

    public function show(CateringPackage $cateringPackage)
    {
        $cateringPackage->load(['city', 'photos', 'bonuses', 'category',
        'kitchen', 'testimonials', 'tiers', 'tiers.benefits']);
        $cateringPackage->kitchen->loadCount('cateringPackages');
        return new CateringPackageApiResource($cateringPackage);
    }
}
