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

        $totalWallet = $users->sum(function ($user) {
            return $user->wallet ?? 0; // Sum of the 'wallet' column
        });

        $totalMember = $users->count(); // ✅ Correct variable name

        return view('admin.dashboard', compact('users', 'totalWallet', 'totalMember'));
    }



    private function getTeamSubordinates($user)
    {
        $team = [];
        foreach ($user->directSubordinates as $direct) {
            $team[] = $direct;
            $team = array_merge($team, $this->getTeamSubordinates($direct));
        }
        return $team;
    }

    public function showLeaderRequests()
    {
        $requests = LeaderRequest::with('user')
            ->where('sent_to', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('requests'));
    }

    public function updateLeaderRequest(Request $request, $id)
    {
        $leaderRequest = LeaderRequest::findOrFail($id);
        $leaderRequest->Leader_status = $request->input('Leader_status');
        $leaderRequest->save();

        if ($leaderRequest->Leader_status == 3) {
            $leaderRequest->user->update(['role' => 'leader']);
        }

        return redirect()->back()->with('success', 'Leader request updated.');
    }

    public function leader_request_data()
    {
        $result = User::where('Leader_status', '>', 0)->get();
        return view('admin.leaderData', compact('result'));
    }

    public function accept_leader($id)
    {
        User::where('id', $id)->update([
            'Leader_status' => 3,
            'role' => 'leader'
        ]);
        return back()->with('success', 'User promoted to Leader.');
    }

    public function reject_leader($id)
    {
        User::where('id', $id)->update([
            'Leader_status' => 2,
            'role' => 'user'
        ]);
        return back()->with('error', 'Leader request rejected.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.memberlist.adminlist')->with('success', 'User deleted successfully.');
    }

    public function promotion()
    {
        $users = User::withCount(['directSubordinates'])->get();

        foreach ($users as $user) {
            $user->team_subordinates_count = count($this->getTeamSubordinates($user));
        }

        return view('admin.promotion', compact('users'));
    }

    public function pending()
    {
        $users = User::where('Leader_status', 1)->get();
        return view('admin.pending', compact('users'));
    }

    public function approved()
    {
        $users = User::where('Leader_status', 3)->get();
        return view('admin.approved', compact('users'));
    }


    public function adminlist()
    {
        $users = User::withCount('directSubordinates')->get();

        foreach ($users as $user) {
            $user->team_subordinates_count = count($this->getTeamSubordinates($user));
        }

        return view('admin.memberlist.adminlist', compact('users'));
    }
}
