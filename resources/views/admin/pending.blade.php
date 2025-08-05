@extends('admin.layouts.app')

@section('content')
<h2>Pending Leader Requests</h2>

<table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
    <thead>
        <tr>
            <th>UID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Promote</th>
            <th>Reject</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>Pending</td>
            <td>
                <a href="{{ route('admin.acceptLeader', $user->id) }}">Promote</a>
            </td>
            <td>
                <a href="{{ route('admin.rejectLeader', $user->id) }}">Reject</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection