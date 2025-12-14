<?php

namespace App\Http\Controllers\Api;

use App\Enums\IncidentStatus;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->incidents()->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'photo' => 'nullable|file|image|max:10240', // 10MB
        ]);

        $path = null;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('incidents', 'public');
        }

        $incident = Incident::create([
            'resident_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'photo_url' => $path ? asset('storage/' . $path) : null,
            'status' => IncidentStatus::Open,
        ]);

        return response()->json($incident, 201);
    }

    public function show(Incident $incident)
    {
        $this->authorize('view', $incident);
        return $incident;
    }
}
