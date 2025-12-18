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

        $prize = $event->prizes()->create([
            'name' => $request->name,
            'stock' => $stock,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hadiah berhasil ditambahkan.',
                'prize' => $prize
            ]);
        }

        return back()->with('success', 'Hadiah berhasil ditambahkan.');
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

        $prize->refresh();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hadiah berhasil diperbarui.',
                'prize' => $prize
            ]);
        }

        return back()->with('success', 'Hadiah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Event $event, Prize $prize)
    {
        if ($prize->event_id !== $event->id) {
            abort(403);
        }

        $prizeId = $prize->id;
        $prize->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hadiah berhasil dihapus.',
                'prize_id' => $prizeId
            ]);
        }

        return back()->with('success', 'Hadiah berhasil dihapus.');
    }
}
