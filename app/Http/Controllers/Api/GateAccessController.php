<?php

namespace App\Http\Controllers\Api;

use App\Enums\GateEntryStatus;
use App\Http\Controllers\Controller;
use App\Models\GateEntry;
use Illuminate\Http\Request;

class GateAccessController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->gateEntries()->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string',
            'entry_date' => 'required|date',
        ]);

        $entry = GateEntry::create([
            'resident_id' => $request->user()->id,
            'guest_name' => $request->guest_name,
            'entry_date' => $request->entry_date,
            'status' => GateEntryStatus::Pending,
        ]);

        return response()->json($entry, 201);
    }
}
