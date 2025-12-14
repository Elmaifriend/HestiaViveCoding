<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->reservations()->with('amenity')->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'amenity_id' => 'required|exists:amenities,id',
            'date' => 'required|date|after:now',
            'duration_hours' => 'required|integer|min:1|max:8',
        ]);

        // Check availability
        $startTime = \Carbon\Carbon::parse($request->date);
        $endTime = $startTime->copy()->addHours($request->duration_hours);

        $exists = Reservation::where('amenity_id', $request->amenity_id)
            ->where('status', '!=', ReservationStatus::Cancelled)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('date', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Time slot not available.'], 422);
        }

        $reservation = Reservation::create([
            'resident_id' => $request->user()->id,
            'amenity_id' => $request->amenity_id,
            'date' => $startTime,
            'end_time' => $endTime,
            'status' => ReservationStatus::Pending,
        ]);

        return response()->json($reservation, 201);
    }
}
