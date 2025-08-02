<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            padding: 20px;
            margin: 0;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        thead {
            background-color: #3b82f6;
            color: white;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f1f5f9;
        }

        tbody tr:hover {
            background-color: #e2e8f0;
        }

        .apply-btn {
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .apply-btn:hover {
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
        }

        .apply-btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            color: #6b7280;
            box-shadow: none;
        }
    </style>

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
</head>

<body>
    <h1>User Dashboard</h1>

    <table>
        <thead>
            <tr>
                <th>UID</th>
                <th>Name</th>
                <th>Direct Subordinates</th>
                <th>Team Subordinates</th>
                <th>Apply for Leader</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userTree as $user)
            <tr>
                <td>{{ $user['uid'] }}</td>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['direct_count'] }}</td>
                <td>{{ $user['team_count'] }}</td>
                <td>
                    @if($user['role'] == "user" && ($user['direct_count'] >= 5 && $user['team_count'] >= 10))
                    <button class="apply-btn" onclick="openModal()">Apply</button>
                    @else
                    <button class="apply-btn" disabled>Not Eligible</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>