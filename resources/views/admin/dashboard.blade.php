@extends('admin.layouts.app')

@section('content')



<!-- Summary Cards -->
<div class="summary-boxes">
    <div class="box">Total Member<br><span id="totalMember">{{ $totalMember }}</span></div>
    <div class="box">Total Wallet<br><span id="totalWallet">₹{{ number_format($totalWallet, 2) }}</span></div>
</div>





@endsection