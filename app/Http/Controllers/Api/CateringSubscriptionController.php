<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCateringSubscribeRequest;
use App\Http\Resources\Api\CateringSubscriptionApiResource;
use App\Models\CateringPackage;
use App\Models\CateringSubscription;
use App\Models\CateringTier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CateringSubscriptionController extends Controller
{
    //
    public function store(StoreCateringSubscribeRequest $request)
    {
        $validatedData = $request->validated();

        $cateringPackage = CateringPackage::find($validatedData['catering_package_id']);

        if (!$cateringPackage) {
            return response()->json(['message' => 'Package not found'], 404);
        }

        $cateringTier = CateringTier::find($validatedData['catering_tier_id']);

        if (!$cateringTier) {
            return response()->json(['message' => 'Tier package not found, please choose the existing tiers available'], 404);
        }

        // Handle file upload
        if ($request->hasFile('proof')) {
            $filePath = $request->file('proof')->store('payment/proofs', 'public');
            $validatedData['proof'] = $filePath;
        }

        // Calculate ended_at based on started_at and duration
        $startedAt = Carbon::parse($validatedData['started_at']);
        $endedAt = $startedAt->copy()->addDays($cateringTier->duration);

        $price = $cateringTier->price;
        $tax = 0.11;
        $totalTax = $tax * $price;
        $grandTotal = $price + $totalTax;

        $validatedData['price'] = $price;
        $validatedData['total_tax_amount'] = round($totalTax, 2);
        $validatedData['total_amount'] = round($grandTotal, 2);

        $validatedData['quantity'] = $cateringTier->quantity;
        $validatedData['duration'] = $cateringTier->duration;
        $validatedData['city'] = $cateringPackage->city->name;
        $validatedData['delivery_time'] = "Lunch time";

        // Add started_at and ended_at to validated data
        $validatedData['started_at'] = $startedAt->format('Y-m-d');
        $validatedData['ended_at'] = $endedAt->format('Y-m-d');

        $validatedData['is_paid'] = false;

        $validatedData['booking_trx_id'] = CateringSubscription::generateUniqueTrxId();

        $bookingTransaction = CateringSubscription::create($validatedData);

        $bookingTransaction->load(['cateringPackage', 'cateringTier']);

        return new CateringSubscriptionApiResource($bookingTransaction);

    }

    public function booking_details(Request $request){

        $request->validate([
            'phone' => 'required|string',
            'booking_trx_id' => 'required|string',
        ]);
        
        $booking = CateringSubscription::where('phone', $request->phone)
            ->where('booking_trx_id', $request->booking_trx_id)
            ->with([
                'cateringPackage',
                'cateringPackage.kitchen',
                'cateringTier',
            ])
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        return new CateringSubscriptionApiResource($booking);
    }

}
