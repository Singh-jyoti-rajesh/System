<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeaderRequest;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Load users with subordinate counts
        $users = User::withCount(['directSubordinates'])->get();

        // Add custom recursive team count
        foreach ($users as $user) {
            $user->team_subordinates_count = count($this->getTeamSubordinates($user));
        }

        // Compute total balances and total members
        $totalBalance = $users->sum(function ($user) {
            return $user->balance ?? 0;
        });

        // Load all leader requests with related user
        $requests = LeaderRequest::with('user')->latest()->get();

        return view('admin.dashboard', [
            'users' => $users,
            'totalBalance' => $totalBalance,
            'totalMember' => $users->count(),
            'requests' => $requests
        ]);
    }

    // Recursive function to get all team subordinates
    private function getTeamSubordinates(User $user)
    {
        $team = [];
        foreach ($user->directSubordinates as $direct) {
            $team[] = $direct;
            $team = array_merge($team, $this->getTeamSubordinates($direct));
        }
        return $team;
    }

    // Handle leader request approval or rejection
    public function updateLeaderRequest(Request $request, $id)
    {
        $leaderRequest = LeaderRequest::findOrFail($id);
        $leaderRequest->Leader_status = $request->input('Leader_status');
        $leaderRequest->save();

        return redirect()->back()->with('success', 'Leader request updated.');
    }


    public function showLeaderRequests()
    {
        // Sirf admin ke liye requests fetch karna
        $requests = LeaderRequest::with('user') // user relation ke saath
            ->where('sent_to', Auth::id()) // jise ye request bheji gayi
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('requests'));
    }
    public function leader_request_data()
    {
        $result = User::where('Leader_status', '>', 0)->get();
        //  dd($result);
        return view('admin.leaderData', compact('result'));
    }
    public function accept_leader($id)
    {
        $result = User::where('id', $id)->update(['Leader_status' => 3]);
        return back();
    }
    public function reject_leader($id)
    {
        $result = User::where('id', $id)->update(['Leader_status' => 2]);
        return back();
    }
}
