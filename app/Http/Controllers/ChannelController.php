<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::paginate(15);
        return view('channels.index', compact('channels'));
    }

    public function create()
    {
        return view('channels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        Channel::create($validated);
        return redirect()->route('channels.index')->with('success', 'Channel created successfully.');
    }

    public function edit(Channel $channel)
    {
        return view('channels.edit', compact('channel'));
    }

    public function update(Request $request, Channel $channel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $channel->update($validated);
        return redirect()->route('channels.index')->with('success', 'Channel updated successfully.');
    }

    public function destroy(Channel $channel)
    {
        try {
            // Check if channel has any sales targets
            $targetsCount = \App\Models\SalesTarget::where('channel_id', $channel->id)->count();
            if ($targetsCount > 0) {
                return redirect()->route('channels.index')
                    ->with('error', "Cannot delete channel '{$channel->name}'. This channel has {$targetsCount} sales target(s) assigned. Please reassign or delete the targets first.");
            }

            // Check if channel has any salesmen
            if ($channel->salesmen()->exists()) {
                return redirect()->route('channels.index')
                    ->with('error', 'Cannot delete channel. Please reassign or delete associated salesmen first.');
            }

            $channel->delete();
            return redirect()->route('channels.index')
                ->with('success', 'Channel deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('channels.index')
                ->with('error', 'Failed to delete channel. Please try again.');
        }
    }
} 