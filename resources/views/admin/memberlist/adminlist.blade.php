@extends('admin.layouts.app')

@section('content')
<style>
    .apply-btn {
        position: absolute;
        left: 1100px;
        top: 146px;
        padding: 10px 20px;
        background: linear-gradient(45deg, #4facfe, #00f2fe);
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .game-btn {
        position: absolute;
        left: 250px;
        top: 146px;
        padding: 10px 20px;
        background: linear-gradient(45deg, #ff416c, #ff4b2b);
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<h2>Admin Dashboard</h2>

<!-- Search Box -->
<div class="search-container">
    <input type="text" id="searchInput" placeholder="Search by UID or User Type...">
</div>
<!-- Game Button (Left Side) -->
<a href="{{ route('user.dashboard30') }}"><button class="game-btn"> Game</button></a>
<!-- Leader Request Button -->
<button class="apply-btn" onclick="openModal()">Leader Request</button>
{{-- <a href="{{ route('admin.leader.request') }}"><button class="apply-btn">Leader Request</button></a> --}}
{{-- <a href="{{ url('admin/leader-request') }}"><button class="apply-btn">Leader Request</button></a> --}}
<!-- Modal -->
<div id="leaderModal" class="modal" onclick="closeModalOutside(event)">
    <div class="modal-content">
        <h3>Leader Request</h3>
        <div class="modal-buttons">
            <button class="approve-btn" onclick="approveRequest()">Approve</button>
            <button class="reject-btn" onclick="rejectRequest()">Reject</button>
        </div>
    </div>
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

        function openModal() {
                document.getElementById('leaderModal').style.display = 'block';
            }
    
            function closeModal() {
                document.getElementById('leaderModal').style.display = 'none';
            }
    
            function closeModalOutside(event) {
                if (event.target.id === 'leaderModal') {
                    closeModal();
                }
            }
    
            function approveRequest() {
                alert("Leader request approved!");
                closeModal();
            }
    
            function rejectRequest() {
                alert("Leader request rejected.");
                closeModal();
            }
    
</script>
@endsection