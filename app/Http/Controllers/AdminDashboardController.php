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
        $users = User::withCount(['directSubordinates'])->get();

        foreach ($users as $user) {
            $user->team_subordinates_count = count($this->getTeamSubordinates($user));
        }

        $totalBalance = $users->sum(function ($user) {
            return $user->balance ?? 0;
        });

        $requests = LeaderRequest::with('user')->latest()->get();

        return view('admin.dashboard', [
            'users' => $users,
            'totalBalance' => $totalBalance,
            'totalMember' => $users->count(),
            'requests' => $requests
        ]);
    }

    private function getTeamSubordinates(User $user)
    {
        $team = [];
        foreach ($user->directSubordinates as $direct) {
            $team[] = $direct;
            $team = array_merge($team, $this->getTeamSubordinates($direct));
        }
        return $team;
    }

    // Update from dashboard form
    public function updateLeaderRequest(Request $request, $id)
    {
        $leaderRequest = LeaderRequest::findOrFail($id);
        $leaderRequest->Leader_status = $request->input('Leader_status');
        $leaderRequest->save();

        // Update user role if approved
        if ($leaderRequest->Leader_status == 3) {
            $leaderRequest->user->update(['role' => 'leader']);
        }

        return redirect()->back()->with('success', 'Leader request updated.');
    }

    public function showLeaderRequests()
    {
        $requests = LeaderRequest::with('user')
            ->where('sent_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('requests'));
    }

    public function leader_request_data()
    {
        $result = User::where('Leader_status', '>', 0)->get();
        return view('admin.leaderData', compact('result'));
    }

    public function accept_leader($id)
    {
        $result = User::where('id', $id)->update([
            'Leader_status' => 3,
            'role' => 'leader'
        ]);
        return back()->with('success', 'User promoted to Leader.');
    }

    public function reject_leader($id)
    {
        $result = User::where('id', $id)->update([
            'Leader_status' => 2,
            'role' => 'user'
        ]);
        return back()->with('error', 'Leader request rejected.');
    }
}
