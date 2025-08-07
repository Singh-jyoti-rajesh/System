<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Wallet Balance</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            background: linear-gradient(135deg, #fefefe, #e0f7fa);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.02);
        }

        .wallet-heading {
            color: #007bff;
        }

        .wallet-amount {
            color: #28a745;
            font-weight: bold;
        }

        .user-id {
            color: #6c757d;
            font-weight: 500;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center p-4">
                    <h2 class="wallet-heading mb-4">Wallet Overview</h2>
                    <h4 class="wallet-amount">Wallet Balance: ₹{{ number_format($user->wallet, 2) }}</h4>
                    <h5 class="user-id mt-3">User ID: #{{ $user->id }}</h5>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional if you use any Bootstrap JS features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>