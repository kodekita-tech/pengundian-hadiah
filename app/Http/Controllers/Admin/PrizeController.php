<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Prize;

class PrizeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'nullable|integer|min:0',
            'is_unlimited' => 'nullable|boolean',
        ]);

        $stock = $request->boolean('is_unlimited') ? null : $request->stock;

        $event->prizes()->create([
            'name' => $request->name,
            'stock' => $stock,
        ]);

        return back()->with('success', 'Prize added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event, Prize $prize)
    {
        // Ensure prize belongs to event
        if ($prize->event_id !== $event->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'nullable|integer|min:0',
            'is_unlimited' => 'nullable|boolean',
        ]);

        $stock = $request->boolean('is_unlimited') ? null : $request->stock;

        $prize->update([
            'name' => $request->name,
            'stock' => $stock,
        ]);

        return back()->with('success', 'Prize updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event, Prize $prize)
    {
        if ($prize->event_id !== $event->id) {
            abort(403);
        }

        $prize->delete();

        return back()->with('success', 'Prize deleted successfully.');
    }
}
