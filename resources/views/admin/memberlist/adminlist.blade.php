@extends('admin.layouts.app')

@section('content')
<h2>Admin Dashboard</h2>

<!-- Search Box -->
<div class="search-container" >
    <input type="text" id="searchInput" placeholder="Search by UID or User Type...">
</div>

<!-- Scrollable Table -->
<div style="overflow-x: auto; border: 1px solid #ccc;">

    <table border="1" cellpadding="10" cellspacing="0" style="min-width: 800px; width: 100%;">
        <thead>
            <tr>
                <th>UID</th>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Direct Subordinates</th>
                <th>Team Subordinates</th>
                <th>Wallet</th>
            </tr>
        </thead>
        <tbody id="userTableBody">
            @foreach ($users as $user)
            <tr>
                <td>{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>{{ $user->direct_subordinates_count ?? 0 }}</td>
                <td>{{ $user->team_subordinates_count ?? 0 }}</td>
                <td>₹{{ $user->wallet ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>



<!-- JavaScript: Search Logic -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#userTableBody tr');

        rows.forEach(row => {
            const uid = row.children[0].textContent.toLowerCase();
            const userType = row.children[3].textContent.toLowerCase();

            if (uid.includes(filter) || userType.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection