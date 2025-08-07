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

        /* Modal Styling */
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
        <h2>Welcome, {{ Auth::user()->name }}!</h2>
        <p><strong>Wallet Balance:</strong> ₹{{ Auth::user()->wallet }}</p>

        <!-- ✅ Timer and Counter placed right below Wallet -->
        <div class="scoreboard">
            <div>⏱️ Time Left: <span id="timer">00:30</span></div>
            <div>🧾 Counter ID: <span id="level">11230001</span></div>
            <span>Switch to:</span>
            <a href="{{ route('user.dashboard') }}"
                style="display: inline-block; background: #007bff; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; text-decoration: none; margin-left: 5px;">
                30sec
            </a>
            <a href="{{ route('user.dashboard1') }}"
                style="display: inline-block; background: #007bff; color: white; padding: 5px 10px; border-radius: 50%; font-size: 12px; text-decoration: none; margin-left: 5px;">
                1min
            </a>


        </div>

        <h3>Select a Game:</h3>
        <button onclick="openGameModal('big')">Play Big</button>
        <button onclick="openGameModal('small')">Play Small</button>

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
            <form method="POST" action="{{ route('game.submit') }}">
                @csrf
                <input type="hidden" name="game_type" id="game_type">
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
            <form method="POST" action="{{ route('game.submit') }}">
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

//     function submitColorBet(amount) {
//     document.getElementById('color_amount').value = amount;
//     document.querySelector('#colorModal form').submit();
// }


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

    <script>
        let timerElement = document.getElementById('timer');
    let levelElement = document.getElementById('level');

    // Check localStorage for saved state
    let savedTime = localStorage.getItem('remainingTime');
    let savedCounter = localStorage.getItem('counterValue');
    let lastSaved = localStorage.getItem('lastSavedAt');

    let currentTime = Math.floor(Date.now() / 1000); // current time in seconds
    let timeInSeconds = 30;
    let counterValue = 11230001;

    if (savedTime && savedCounter && lastSaved) {
        let timePassed = currentTime - parseInt(lastSaved);
        let remaining = parseInt(savedTime) - timePassed;

        if (remaining > 0) {
            timeInSeconds = remaining;
            counterValue = parseInt(savedCounter);
        } else {
            let cyclesPassed = Math.floor((timePassed - parseInt(savedTime)) / 30) + 1;
            counterValue = parseInt(savedCounter) + cyclesPassed;
            timeInSeconds = 30 - ((timePassed - parseInt(savedTime)) % 30);
        }
    } 

    function updateTimerDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

  function startCountdown() {
    setInterval(() => {
        timeInSeconds--;
        updateTimerDisplay(timeInSeconds);

        // ✅ Save state to localStorage on every tick
        localStorage.setItem('remainingTime', timeInSeconds);
        localStorage.setItem('counterValue', counterValue);
        localStorage.setItem('lastSavedAt', Math.floor(Date.now() / 1000));

        if (timeInSeconds <= 0) {
            timeInSeconds = 30; // changed from 30 to 60 for 1 min timer
            counterValue++;
            levelElement.textContent = counterValue.toString().padStart(10, '0');

            // Call backend to check game results
            checkGameResult('big');
            checkGameResult('small');
            checkColorResult();
        }
    }, 1000);
}

    updateTimerDisplay(timeInSeconds);  
    startCountdown();


    function checkGameResult(type) {
    fetch(`/game/result/${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'none') {
                alert(data.message); // Show WIN or LOSE
            }
        });
}

function checkColorResult() {
    fetch(`/check-color-result`)
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