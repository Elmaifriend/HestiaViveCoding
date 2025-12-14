<?php

namespace App\Http\Controllers\Api;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->invitations()->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'expiration_hours' => 'nullable|integer|min:1|max:168', // default 24h, max 1 week
        ]);

        $invitation = Invitation::create([
            'resident_id' => $request->user()->id,
            'qr_code' => Str::upper(Str::random(12)),
            'expiration_date' => now()->addHours($request->expiration_hours ?? 24),
            'status' => InvitationStatus::Active,
        ]);

        return response()->json($invitation, 201);
    }

    public function show(Invitation $invitation)
    {
        $this->authorize('view', $invitation);
        return $invitation;
    }

    public function validateQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $invitation = Invitation::where('qr_code', $request->qr_code)->first();

        if (! $invitation) {
            return response()->json(['message' => 'Invalid QR code.'], 404);
        }

        if ($invitation->status !== InvitationStatus::Active) {
            return response()->json(['message' => 'Invitation is not active.', 'status' => $invitation->status], 400);
        }

        if ($invitation->expiration_date < now()) {
            $invitation->update(['status' => InvitationStatus::Expired]);
            return response()->json(['message' => 'Invitation expired.', 'status' => 'expired'], 400);
        }

        // Mark as used? Or just validate? 
        // For simpler access control, maybe guard marks it as used manually or auto. 
        // I'll assume validation is read-only unless `mark_used` flag is sent.
        
        if ($request->boolean('mark_used')) {
             $invitation->update(['status' => InvitationStatus::Used]);
        }

        return response()->json(['message' => 'Valid QR.', 'invitation' => $invitation]);
    }
}
