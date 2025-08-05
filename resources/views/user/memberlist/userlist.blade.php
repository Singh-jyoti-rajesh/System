@extends('admin.layouts.app')

@section('content')



<script>
    function openModal() {
            const confirmApply = confirm("Do you want to apply for leader?");
            if (confirmApply) {
                $.ajax({
                    url: '/apply-leader',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        alert("Leader request sent to admin!");
                        location.reload();
                    },
                    error: function(xhr) {
                        alert("Something went wrong.");
                    }
                });
            }
        }
</script>

<h1>Leader List</h1>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>UID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Direct Subordinates</th>
                <th>Team Subordinates</th>
                <th>Apply for Leader</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userTree as $user)
            <tr>
                <td>{{ str_pad($user['uid'], 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['email'] }}</td>
                <td>{{ $user['direct_count'] }}</td>
                <td>{{ $user['team_count'] }}</td>
                <td>
                    @if($user['role'] == 'user' && $user['direct_count'] >= 5 && $user['team_count'] >= 10 &&
                    $user['invited_by_qualifies'])
                    <button class="apply-btn" onclick="openModal()">Apply</button>
                    @else
                    <button class="apply-btn" disabled>Not Eligible</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection