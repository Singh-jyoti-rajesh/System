@extends('admin.layouts.app')

@section('content')
<h2>Promotion Dashboard</h2>

<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>UID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Direct Subordinates</th>
            <th>Team Subordinates</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->direct_subordinates_count ?? 0 }}</td>
            <td>{{ $user->team_subordinates_count ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection