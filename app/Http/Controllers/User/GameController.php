<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{

 public function store(Request $request)
{
    $request->validate([
        'balance' => 'required|integer',
        'quantity' => 'required|integer|min:1',
        'multiplier' => 'required|integer',
        'total_amount' => 'required|integer',
        // 'game_type' => 'required|in:big,small',
        'game_type' => 'required|in:big,small,color',

    ]);

    $user = Auth::user();

    // Check wallet
    if ($user->wallet < $request->total_amount) {
        return back()->with('error', 'Insufficient wallet balance.');
    }

    // Deduct from wallet
    $user->wallet -= $request->total_amount;
    $user->save();

    // Save Game Entry
    $gameData = [
        'user_id' => $user->id,
        'email' => $user->email,
        'game_type' => $request->game_type,
        'balance' => $request->balance,
        'quantity' => $request->quantity,
        'multiplier' => $request->multiplier,
        'total_amount' => $request->total_amount,
        'result' => 'pending',
    ];

    // Handle optional color-based bet
    if ($request->has('color') && $request->has('color_bet')) {
        $gameData['color'] = $request->color;
        $gameData['color_bet'] = $request->color_bet;
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

    // Count total bets for both sides
    $bigCount = GameOrder::where('game_type', 'big')->where('created_at', '>=', now()->subSeconds(30))->sum('total_amount');
    $smallCount = GameOrder::where('game_type', 'small')->where('created_at', '>=', now()->subSeconds(30))->sum('total_amount');

    $winner = $bigCount < $smallCount ? 'big' : 'small';

    // Set result
    $latest->result = ($latest->game_type === $winner) ? 'win' : 'lose';
    $latest->save();

    // If win, add back double to wallet
    if ($latest->result === 'win') {
        $user->wallet += $latest->total_amount * 2; // or multiplier logic
        $user->save();
    }

    return response()->json([
        'status' => $latest->result,
        'message' => "You " . strtoupper($latest->result) . "!"
    ]);
}

// Submit Color Bet
public function submitColorBet(Request $request)
{
    $request->validate([
        'color' => 'required|in:Red,Green,Violet',
        'amount' => 'required|integer|min:1',
    ]);

    $user = Auth::user();

    if ($user->wallet < $request->amount) {
        return back()->with('error', 'Insufficient wallet balance.');
    }

    $user->wallet -= $request->amount;
    $user->save();

    GameOrder::create([
        'user_id' => $user->id,
        'email' => $user->email,
        'game_type' => 'color',
        'color' => $request->color,
        'total_amount' => $request->amount,
        'result' => 'pending',
    ]);

    return back()->with('success', 'Bet placed on color ' . $request->color);
}

// Check color result (to be called after 30 seconds)
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

    // Simulate result randomly
    $colors = ['Red', 'Green', 'Violet'];
    $winningColor = $colors[array_rand($colors)];

    $order->result = ($order->color === $winningColor) ? 'win' : 'lose';
    $order->save();

    if ($order->result === 'win') {
        $user->wallet += $order->total_amount * 2;
        $user->save();
    }

    return response()->json([
        'status' => $order->result,
        'color' => $winningColor,
        'message' => $order->result === 'win'
            ? "You WON on color: {$winningColor}!"
            : "You LOST. Winning color was {$winningColor}."
    ]);
}



}
