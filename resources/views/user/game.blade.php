<!DOCTYPE html>
<html>

<head>
    <title>{{ ucfirst($gameType) }} Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 30px;
            border: 1px solid #ccc;
            width: 340px;
            margin: 0 auto;
            text-align: center;
        }

        .btn-group button {
            margin: 5px;
            padding: 10px 15px;
        }

        input[type=number] {
            width: 60px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>WinGo 30sec</h2>
        <h3>Select: {{ ucfirst($gameType) }}</h3>

        <div id="timer"></div>
        <div id="result-message" style="color: green;"></div>


        <form method="POST" action="{{ route('game.submit') }}">
            @csrf

            <input type="hidden" name="game_type" value="{{ $gameType }}">
            <div>
                <strong>Balance:</strong><br>
                <button type="button" onclick="setBalance(5)">₹5</button>
                <button type="button" onclick="setBalance(10)">₹10</button>
                <button type="button" onclick="setBalance(20)">₹20</button>
                <input type="hidden" name="balance" id="balance" value="5">
            </div>

            <div style="margin-top: 20px;">
                <strong>Quantity:</strong><br>
                <button type="button" onclick="updateQty(-1)">-</button>
                <input type="number" name="quantity" id="quantity" value="1" readonly>
                <button type="button" onclick="updateQty(1)">+</button>
            </div>

            <div style="margin-top: 20px;">
                <strong>Multiplier:</strong><br>
                <button type="button" onclick="setMultiplier(1)">X1</button>
                <button type="button" onclick="setMultiplier(5)">X5</button>
                <button type="button" onclick="setMultiplier(10)">X10</button>
                <input type="hidden" name="multiplier" id="multiplier" value="1">
            </div>

            <div style="margin-top: 20px;">
                <strong>Total Amount: ₹<span id="total_display">5</span></strong>
                <input type="hidden" name="total_amount" id="total_amount" value="5">
            </div>

            <div style="margin-top: 20px;">
                <input type="checkbox" required checked> I agree to <a href="#">《Pre-sale rules》</a>
            </div>

            <div style="margin-top: 20px;">
                <button type="submit" id="submitBtn">Submit</button>
            </div>
        </form>
    </div>

    <script>
        function setBalance(val) {
        document.getElementById('balance').value = val;
        calcTotal();
    }

    function setMultiplier(val) {
        document.getElementById('multiplier').value = val;
        calcTotal();
    }

    function updateQty(change) {
        let qty = parseInt(document.getElementById('quantity').value);
        qty += change;
        if (qty < 1) qty = 1;
        document.getElementById('quantity').value = qty;
        calcTotal();
    }

    function calcTotal() {
        const balance = parseInt(document.getElementById('balance').value);
        const qty = parseInt(document.getElementById('quantity').value);
        const multi = parseInt(document.getElementById('multiplier').value);
        const total = balance * qty * multi;

        document.getElementById('total_display').innerText = total;
        document.getElementById('total_amount').value = total;
    }

    window.onload = calcTotal;
    </script>

    <script>
        let timeLeft = 30;
    const timerEl = document.getElementById('timer');
    const submitBtn = document.getElementById('submitBtn');
    const resultEl = document.getElementById('result-message');

    const interval = setInterval(() => {
        timeLeft--;
        timerEl.innerText = "Time left: " + timeLeft + "s";

        if (timeLeft <= 5) {
            submitBtn.style.display = 'none'; // Hide after 25s
        }

        if (timeLeft <= 0) {
            clearInterval(interval);
            showResult();
        }
    }, 1000);

    function showResult() {
        fetch("{{ route('game.result.check', ['type' => $gameType]) }}")
            .then(res => res.json())
            .then(data => {
                resultEl.innerText = data.message;
                resultEl.style.color = data.status === "win" ? "green" : "red";
            });
    }
    </script>