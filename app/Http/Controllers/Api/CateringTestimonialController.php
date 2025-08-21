<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CateringTestimonialApiResource;
use App\Models\CateringTestimonial;
use Illuminate\Http\Request;

class CateringTestimonialController extends Controller
{
    //
    public function index()
    {
    $testimonials = CateringTestimonial::with(['cateringPackage'])->get();
    return CateringTestimonialApiResource::collection($testimonials);
    }
}
