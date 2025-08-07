@extends('admin.layouts.app')

@section('content')
<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* body {
            font-family: Arial, sans-serif;
            background: #f4f4f9;
            padding: 50px;
            margin: 0;
        } */

        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        button {
            margin: 5px;
            padding: 10px 15px;
        }

        .scoreboard {
            margin-top: 15px;
            margin-bottom: 25px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            background: #f0f0f0;
            padding: 10px;
            border-radius: 8px;
        }

        .number-balls {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            justify-items: center;
            margin: 20px 0;
        }

        .ball {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .ball.red {
            background-color: red;
        }

        .ball.green {
            background-color: green;
        }

        .ball.violet {
            background-color: purple;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 99;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .close {
            float: right;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Welcome 5Min, {{ Auth::user()->name }}!</h2>
        <p><strong>Wallet Balance:</strong> ₹{{ Auth::user()->wallet }}</p>

        <!-- ✅ Timer and Counter placed right below Wallet -->
        <div class="scoreboard">
            <div>⏱️ Time Left: <span id="timer">00:30</span></div>
            <div>🧾 Counter ID: <span id="level">11230001</span></div>
            <span>Switch to:</span>
            <a href="{{ route('user.dashboard30') }}"
                style="display: inline-block; background: #007bff; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; text-decoration: none; margin-left: 5px;">
                30sec
            </a>
            <a href="{{ route('user.dashboard1') }}"
                style="display: inline-block; background: #007bff; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; text-decoration: none; margin-left: 5px;">
                1min
            </a>

            <a href="{{ route('user.dashboard3') }}"
                style="display: inline-block; background: #007bff; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; text-decoration: none; margin-left: 5px;">
                3min
            </a>

            <a href="{{ route('user.dashboard5') }}"
                style="display: inline-block; background: #007bff; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; text-decoration: none; margin-left: 5px;">
                5min
            </a>


        </div>

        <h3>Select a Game:</h3>
        <button onclick="openGameModal('big')">Play Big</button>
        <button onclick="openGameModal('small')">Play Small</button>

        <h3>Select Number:</h3>
        <div class="number-balls">
            @php
            $ballColors = ['violet', 'green', 'red', 'green', 'red', 'violet', 'red', 'green', 'red', 'green'];
            @endphp

            @foreach(range(0, 9) as $i)
            <!-- <button class="ball {{ $ballColors[$i] }}" onclick="openNumberBet({{$i}})">{{ $i }}</button> -->
            <button class="ball {{ $ballColors[$i] }}" onclick="openNumberBet('{{ $i }}')">{{ $i }}</button>
            @endforeach
        </div>

        <h3>Select Color:</h3>
        <button onclick="openColorModal('Red')" style="background: red; color: white;">Red</button>
        <button onclick="openColorModal('Green')" style="background: green; color: white;">Green</button>
        <button onclick="openColorModal('Violet')" style="background: purple; color: white;">Violet</button>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <!-- Game Modal -->
    <div id="gameModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('gameModal')">&times;</span>
            <h2>WinGo 30sec</h2>
            <h3 id="gameTypeTitle">Game</h3>
            <form method="POST" action="{{ route('game5.submit') }}">
                @csrf
                <input type="hidden" name="game_type" id="game_type">
                <input type="hidden" name="number" id="selectedNumber">
                <input type="hidden" name="balance" id="balance" value="5">
                <input type="hidden" name="multiplier" id="multiplier" value="1">

                <div>
                    <strong>Balance:</strong><br>
                    <button type="button" onclick="setBalance(5)">₹5</button>
                    <button type="button" onclick="setBalance(10)">₹10</button>
                    <button type="button" onclick="setBalance(20)">₹20</button>
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
                </div>

                <div style="margin-top: 20px;">
                    <strong>Total Amount: ₹<span id="total_display">5</span></strong>
                    <input type="hidden" name="total_amount" id="total_amount" value="5">
                </div>

                <div style="margin-top: 20px;">
                    <input type="checkbox" required checked> I agree to <a href="#">《Pre-sale rules》</a>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Color Modal -->
    <div id="colorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('colorModal')">&times;</span>
            <h2 id="colorTitle">Bet on Color</h2>
            <form method="POST" action="{{ route('game5.submit') }}">
                @csrf
                <input type="hidden" name="game_type" value="color">
                <input type="hidden" name="color" id="selectedColor">
                <input type="hidden" name="amount" id="color_amount" value="5"> <!-- ✅ Required for controller -->
                <input type="hidden" name="balance" value="5">
                <input type="hidden" name="multiplier" value="1">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="total_amount" value="5">

                <p>Select amount:</p>
                <button type="button" onclick="submitColorBet(5)">₹5</button>
                <button type="button" onclick="submitColorBet(10)">₹10</button>
                <button type="button" onclick="submitColorBet(20)">₹20</button>
            </form>
        </div>
    </div>


    <!-- JS Logic -->
    <script>
        function openGameModal(type) {
        document.getElementById('game_type').value = type;
        document.getElementById('gameTypeTitle').innerText = "Play " + type.charAt(0).toUpperCase() + type.slice(1);
        document.getElementById('gameModal').style.display = 'block';
        calcTotal();
    }

     function openNumberBet(number) {
        document.getElementById('game_type').value = 'number';
        document.getElementById('gameTypeTitle').innerText = 'Number: ' + number;
        document.getElementById('selectedNumber').value = number;
        document.getElementById('gameModal').style.display = 'block';
        calculateTotal();
    }

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

    function openColorModal(color) {
        document.getElementById('selectedColor').value = color;
        document.getElementById('colorTitle').innerText = `Place bet on ${color}`;
        document.getElementById('colorModal').style.display = 'block';
    }

    function submitColorBet(amount) {
        document.getElementById('color_amount').value = amount;
        document.querySelector('#colorModal input[name="balance"]').value = amount;
        document.querySelector('#colorModal input[name="total_amount"]').value = amount;
        document.querySelector('#colorModal input[name="amount"]').value = amount;
        document.querySelector('#colorModal form').submit();
    }

    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    window.onclick = function(event) {
        ['gameModal', 'colorModal'].forEach(id => {
            const modal = document.getElementById(id);
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }
    </script>

    <!-- ✅ New Timer Script (Backend-integrated) -->
    <script>
        let timerElement = document.getElementById('timer');
let levelElement = document.getElementById('level');
let timeInSeconds = 300; // ✅ 5 minutes = 300 seconds
let counterValue = 11230001;

function updateTimerDisplay(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

  function startCountdown() {
    setInterval(() => {
        timeInSeconds--;
        updateTimerDisplay(timeInSeconds);

        if (timeInSeconds <= 0) {
            timeInSeconds = 300;
            counterValue++;
            levelElement.textContent = counterValue.toString().padStart(10, '0');

            // Result check
            checkGameResult('big');
            checkGameResult('small');
            checkGameResult('number');  // ✅ Add this line
            checkColorResult();
        }
    }, 1000);
}

function initializeGameRound() {
    fetch('/current-round5') // this should now return correct 5min info
        .then(res => res.json())
        .then(data => {
            if (data.counter_id && data.remaining_time !== undefined) {
                counterValue = parseInt(data.counter_id);
                levelElement.textContent = counterValue.toString().padStart(10, '0');
                timeInSeconds = parseInt(data.remaining_time);
                updateTimerDisplay(timeInSeconds);
                startCountdown();
            } else {
                alert("Unable to fetch current game round.");
            }
        })
        .catch(() => alert("Server error while loading game round."));
}
initializeGameRound();

    function checkGameResult(type) {
        fetch(`/game5/result/${type}`)
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'none') {
                    alert(data.message); // Show WIN or LOSE
                }
            });
    }

    function checkColorResult() {
        fetch(`/check-color-result5`)
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'none') {
                    alert(data.message); // Show Color bet result
                }
            });
    }
    </script>

</body>

</html>
@endsection