<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    public function index()
    {
        $channels = Channel::orderBy('name')->get();
        
        return response()->json([
            'data' => $channels
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'channel_code' => 'required|string|unique:channels,channel_code',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $channel = Channel::create($request->all());

        return response()->json([
            'data' => $channel,
            'message' => 'Channel created successfully'
        ], 201);
    }

    public function show(Channel $channel)
    {
        return response()->json([
            'data' => $channel
        ]);
    }

    public function update(Request $request, Channel $channel)
    {
        $request->validate([
            'channel_code' => ['required', 'string', Rule::unique('channels')->ignore($channel->id)],
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $channel->update($request->all());

        return response()->json([
            'data' => $channel,
            'message' => 'Channel updated successfully'
        ]);
    }

    public function destroy(Channel $channel)
    {
        $channel->delete();

        return response()->json([
            'message' => 'Channel deleted successfully'
        ]);
    }
} 