<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #eef2f7;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .apply-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            padding: 10px 20px;
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .apply-btn:hover {
            background: linear-gradient(45deg, #00f2fe, #4facfe);
        }

        .summary-boxes {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 30px;
        }

        .box {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            width: 180px;
            text-align: center;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .search-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .search-container input {
            padding: 12px 15px;
            width: 320px;
            border: 2px solid #ccc;
            border-radius: 30px;
            font-size: 16px;
            outline: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        thead {
            background-color: #4facfe;
            color: white;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f7f9fc;
        }

        tbody tr:hover {
            background-color: #e3f2fd;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 320px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .modal-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .modal-buttons button {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .approve-btn {
            background-color: #28a745;
            color: white;
        }

        .approve-btn:hover {
            background-color: #218838;
        }

        .reject-btn {
            background-color: #dc3545;
            color: white;
        }

        .reject-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>

    <!-- Leader Request Button -->
    <a href="{{ url('admin/leader-request') }}"><button class="apply-btn">Leader Request</button></a>

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

    <h1>Admin Panel</h1>

    <!-- Summary Cards -->
    <div class="summary-boxes">
        <div class="box">Total Member<br><span id="totalMember">{{ $totalMember }}</span></div>
        <div class="box">Total Balance<br><span id="totalBalance">₹{{ number_format($totalBalance, 2) }}</span></div>
    </div>

    <!-- Search -->
    <div class="search-container">
        <input type="text" id="searchInput" placeholder="Search by UID or User Type...">
    </div>

    <!-- User Table -->
    <table>
        <thead>
            <tr>
                <th>UID</th>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Direct Subordinate</th>
                <th>Team Subordinate</th>
                <th>Balance</th>
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
                <td>₹{{ number_format($user->balance, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- JavaScript -->
    <script>
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

        // Search filter logic
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

</body>

</html>