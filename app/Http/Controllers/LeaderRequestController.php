<?php

namespace App\Http\Controllers;

use App\Models\LeaderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class LeaderRequestController extends Controller
{
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->leaderRequest && $user->leaderRequest->Leader_status == 'pending') {
            return response()->json(['message' => 'Already applied.'], 400);
        }

        LeaderRequest::create([
            'user_id' => $user->id,
            'Leader_status' => 'pending',
        ]);

        return response()->json(['message' => 'Request submitted.']);
    }

    public function update(Request $request, $id)
    {
        $leaderRequest = LeaderRequest::findOrFail($id);
        $leaderRequest->Leader_status = $request->input('Leader_status');
        $leaderRequest->save();

        return redirect()->back()->with('Leader_status', 'Request updated.');
    }
    public function applyLeader(Request $request)
    {
        $user = Auth::user();

        // Check agar pehle hi request bheji ho
        $existingRequest = LeaderRequest::where('user_id', $user->id)->first();
        if ($existingRequest) {
            return response()->json(['success' => false, 'message' => 'Aapne pehle hi request bheji hai.']);
        }

        $admin = User::where('role', 'admin')->first();

        LeaderRequest::create([
            'user_id' => $user->id,
            'sent_to' => $admin ? $admin->id : null,
            'Leader_status' => 'pending'
        ]);

        return response()->json(['success' => true, 'message' => 'Leader request bheji gayi hai.']);
    }
}
