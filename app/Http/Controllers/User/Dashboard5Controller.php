<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\GameOrder;
use App\Models\GameRound;

class Dashboard5Controller extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // Find active 5-minute round
        $round = GameRound::where('start_time', '<=', $now)
                          ->where('end_time', '>', $now)
                          ->where('duration_type', '5m')
                          ->first();

        // If no round exists, create a new 5-minute round
        if (!$round) {
            $last = GameRound::where('duration_type', '5m')->latest('counter_id')->first();
            $newCounterId = $last ? $last->counter_id + 1 : 11230001;

            $start = $now->copy()->startOfSecond();
            $end = $start->copy()->addMinutes(5); // ⏱ 5 minutes

            $round = GameRound::create([
                'counter_id'     => $newCounterId,
                'start_time'     => $start,
                'end_time'       => $end,
                'duration_type'  => '5m'
            ]);
        }

        $remaining = $round->end_time->diffInSeconds($now);

        return view('user.dashboard5', [
            'counterId' => $round->counter_id,
            'remainingSeconds' => $remaining,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'balance'      => 'required|integer',
            'quantity'     => 'required|integer|min:1',
            'multiplier'   => 'required|integer',
            'total_amount' => 'required|integer',
            'game_type'    => 'required|in:big,small,color,number',
        ]);

        $user = Auth::user();

        if ($user->wallet < $request->total_amount) {
            return back()->with('error', 'Insufficient wallet balance.');
        }

        $now = Carbon::now();
        $round = GameRound::where('start_time', '<=', $now)
                          ->where('end_time', '>', $now)
                          ->where('duration_type', '5m')
                          ->first();

        if (!$round) {
            return back()->with('error', 'No active game round.');
        }

        $user->wallet -= $request->total_amount;
        $user->save();

        $gameData = [
            'user_id'      => $user->id,
            'email'        => $user->email,
            'game_type'    => $request->game_type,
            'balance'      => $request->balance,
            'quantity'     => $request->quantity,
            'multiplier'   => $request->multiplier,
            'total_amount' => $request->total_amount,
            'result'       => 'pending',
            'round_id'     => $round->id,
        ];

        if ($request->has('color')) {
            $gameData['color'] = $request->color;
        }

        if ($request->has('number')) {
        $gameData['number'] = $request->number; // ✅ save number
    }

        GameOrder::create($gameData);

        return back()->with('success', 'Bet placed successfully.');
    }

    public function checkResult($type)
    {
        $user = Auth::user();

        $latest = GameOrder::where('user_id', $user->id)
                           ->where('game_type', $type)
                           ->latest()
                           ->first();

        if (!$latest || $latest->result !== 'pending') {
            return response()->json(['status' => 'none', 'message' => 'No result found']);
        }

        $round = GameRound::where('id', $latest->round_id)
                          ->where('duration_type', '5m')
                          ->first();

        if (!$round || Carbon::now()->lt($round->end_time)) {
            return response()->json(['status' => 'wait', 'message' => 'Result not available yet.']);
        }

        $bigCount = GameOrder::where('game_type', 'big')
                             ->where('round_id', $round->id)
                             ->sum('total_amount');

        $smallCount = GameOrder::where('game_type', 'small')
                               ->where('round_id', $round->id)
                               ->sum('total_amount');

        $winner = $bigCount < $smallCount ? 'big' : 'small';

        $latest->result = ($latest->game_type === $winner) ? 'win' : 'lose';
        $latest->save();

        if ($latest->result === 'win') {
            $user->wallet += $latest->total_amount * 2;
            $user->save();
        }

        return response()->json([
            'status'  => $latest->result,
            'message' => "You " . strtoupper($latest->result) . "!"
        ]);
    }

    public function checkColorResult()
    {
        $user = Auth::user();

        $order = GameOrder::where('user_id', $user->id)
                          ->where('game_type', 'color')
                          ->where('result', 'pending')
                          ->latest()
                          ->first();

        if (!$order) {
            return response()->json(['status' => 'none', 'message' => 'No pending color bet found.']);
        }

        $round = GameRound::where('id', $order->round_id)
                          ->where('duration_type', '5m')
                          ->first();

        if (!$round || Carbon::now()->lt($round->end_time)) {
            return response()->json(['status' => 'wait', 'message' => 'Result not available yet.']);
        }

        $colors = ['Red', 'Green', 'Violet'];
        $winningColor = $colors[array_rand($colors)];

        $order->result = ($order->color === $winningColor) ? 'win' : 'lose';
        $order->save();

        if ($order->result === 'win') {
            $user->wallet += $order->total_amount * 2;
            $user->save();
        }

        return response()->json([
            'status'  => $order->result,
            'color'   => $winningColor,
            'message' => $order->result === 'win'
                        ? "You WON on color: {$winningColor}!"
                        : "You LOST. Winning color was {$winningColor}."
        ]);
    }

    public function getCurrentRound()
    {
        $round = GameRound::where('duration_type', '5m')
                          ->latest('id')
                          ->first();

        if (!$round) {
            return response()->json(['error' => 'No round found'], 404);
        }

        $remaining = max(0, $round->end_time->diffInSeconds(Carbon::now(), false) * -1);

        return response()->json([
            'counter_id'     => $round->counter_id,
            'remaining_time' => $remaining,
        ]);
    }
}
