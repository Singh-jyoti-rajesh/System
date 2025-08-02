<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LeaderRequest;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $allUsers = User::all();
        function getTeamSubordinates($user, $allUsers)
        {
            $team = collect();
            $directs = $allUsers->where('invited_by', $user->id);
            foreach ($directs as $sub) {
                $team->push($sub);
                $team = $team->merge(getTeamSubordinates($sub, $allUsers));
            }
            return $team;
        }
        $userTree = collect();
        $usersToDisplay = $currentUser->role === 'admin'
            ? $allUsers
            : collect([$currentUser]);
        foreach ($usersToDisplay as $user) {
            $inviter = $allUsers->firstWhere('id', $user->invited_by);
            $directSubordinates = $allUsers->where('invited_by', $user->id);
            $teamSubordinates = getTeamSubordinates($user, $allUsers);
            $userTree->push([
                'uid' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'invitation_code' => $user->invitation_code,
                'invited_by' => $inviter ? $inviter->email : 'N/A',
                'direct_count' => $directSubordinates->count(),
                'team_count' => $teamSubordinates->count(),
                'invited_by_qualifies' => $inviter && (
                    $allUsers->where('invited_by', $inviter->id)->count() >= 5 ||
                    getTeamSubordinates($inviter, $allUsers)->count() >= 10
                )
            ]);
        }
        return view('user.dashboard', compact('userTree'));
    }

    public function applyLeader(Request $request)
    {
        $user = Auth::user();
        $user->Leader_Leader_status = 1;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Request submitted']);
    }
}
