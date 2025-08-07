<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LeaderRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Logged in user
        return view('user.wallet', compact('user'));
    }

    public function account()
    {
        $user = Auth::user(); // Logged in user
        return view('user.account', compact('user'));
    }
    public function promotion()
    {
        $user = Auth::user();

        // Count direct subordinates
        $user->direct_subordinates_count = $user->directSubordinates()->count();

        // Get all team subordinates
        $allTeam = $this->getTeamSubordinates($user);

        // Filter out direct subordinates from the team
        $directIds = $user->directSubordinates()->pluck('id')->toArray();

        $indirectTeam = array_filter($allTeam, function ($member) use ($directIds) {
            return !in_array($member->id, $directIds);
        });

        // Count only indirect team members
        $user->team_subordinates_count = count($indirectTeam);

        return view('user.promotion', compact('user'));
    }


    // Recursive team subordinate function
    private function getTeamSubordinates(User $user)
    {
        $team = [];
        foreach ($user->directSubordinates as $direct) {
            $team[] = $direct;
            $team = array_merge($team, $this->getTeamSubordinates($direct));
        }
        return $team;
    }


    public function showDepositForm()
    {
        $user = Auth::user();
        return view('user.deposit', compact('user'));
    }

    // // Process deposit
    // public function deposit(Request $request)
    // {
    //     $request->validate([
    //         'amount' => 'required|numeric|min:1'
    //     ]);

    //     $user = Auth::user();
    //     $user->wallet += $request->amount;
    //     $user->save();

    //     return redirect()->back()->with('success', 'Amount deposited successfully!');
    // }

    //     // Show withdrawal form
    // public function showWithdrawForm()
    // {
    //     $user = Auth::user();
    //     return view('user.withdraw', compact('user'));
    // }

    // // Process withdrawal
    // public function withdraw(Request $request)
    // {
    //     $request->validate([
    //         'amount' => 'required|numeric|min:1'
    //     ]);

    //     $user = Auth::user();

    //     if ($request->amount > $user->wallet) {
    //         return redirect()->back()->withErrors(['amount' => 'Insufficient wallet balance.']);
    //     }

    //     $user->wallet -= $request->amount;
    //     $user->save();

    //     return redirect()->route('user.withdraw.form')->with('success', 'Amount withdrawn successfully!');
    // }



}
