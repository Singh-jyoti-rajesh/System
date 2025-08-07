
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promotion Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff, #e6f7ff);
            padding: 30px;
        }

        .dashboard-heading {
            color: #0d6efd;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }

        .styled-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .styled-table th, .styled-table td {
            padding: 15px;
            border: 1px solid #dee2e6;
        }

        .styled-table th {
            background-color: #007bff;
            color: white;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>

<div class="container my-5">
    <div class="dashboard-card">
        <h2 class="dashboard-heading">Promotion Dashboard</h2>
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Direct Subordinates</th>
                    <th>Team Subordinates</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $user->direct_subordinates_count ?? 0 }}</td>
                    <td>{{ $user->team_subordinates_count ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
