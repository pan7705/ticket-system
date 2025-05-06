<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Queue Ticketing - Customer View</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .counter-container {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }
        .counter {
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            width: 200px;
        }
        .counter.offline {
            opacity: 0.5;
            background-color: #f5f5f5;
        }
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }
        .status-green {
            background-color: green;
        }
        .status-red {
            background-color: red;
        }
        .status-grey {
            background-color: grey;
        }
        .info-panel {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .btn-take-number {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1 class="text-center">Customer View</h1>

    <div class="info-panel">
        <h3>Now Serving: <span id="now-serving">{{ $nowServingNumber ?? 'None' }}</span></h3>
        <h3>Last Number: <span id="last-number">{{ $lastTicketNumber }}</span></h3>
        <button class="btn-take-number" id="take-number-btn">Take a Number</button>
    </div>

    <div class="counter-container">
        @foreach($counters as $counter)
        <div class="counter {{ !$counter->is_online ? 'offline' : '' }}" id="counter-{{ $counter->id }}">
            <h3>Counter {{ $counter->id }}</h3>
            <div>
                <span class="status-indicator {{ $counter->is_online ? ($counter->is_serving ? 'status-red' : 'status-green') : 'status-grey' }}" id="status-{{ $counter->id }}"></span>
                <span id="counter-number-{{ $counter->id }}">{{ $counter->is_online ? ($counter->current_ticket_number ?? 'Available') : 'Offline' }}</span>
            </div>
        </div>
        @endforeach
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Take a number button
            document.getElementById('take-number-btn').addEventListener('click', function() {
                fetch('/take-number', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert('Your ticket number is: ' + data.ticket_number);
                });
            });

            // Real-time updates with Laravel Echo
            window.Echo.channel('tickets')
                .listen('TicketCreated', (e) => {
                    document.getElementById('last-number').textContent = e.lastTicketNumber;
                })
                .listen('TicketCalled', (e) => {
                    document.getElementById('now-serving').textContent = e.nowServing;

                    const counterId = e.counter.id;
                    document.getElementById(`counter-number-${counterId}`).textContent = e.ticket.ticket_number;
                    document.getElementById(`status-${counterId}`).className = 'status-indicator status-red';
                });

            window.Echo.channel('counters')
                .listen('CounterStatusChanged', (e) => {
                    const counterId = e.counter.id;
                    const counterElement = document.getElementById(`counter-${counterId}`);
                    const statusElement = document.getElementById(`status-${counterId}`);
                    const numberElement = document.getElementById(`counter-number-${counterId}`);

                    if (e.counter.is_online) {
                        counterElement.classList.remove('offline');

                        if (e.counter.is_serving) {
                            statusElement.className = 'status-indicator status-red';
                            numberElement.textContent = e.counter.current_ticket_number;
                        } else {
                            statusElement.className = 'status-indicator status-green';
                            numberElement.textContent = 'Available';
                        }
                    } else {
                        counterElement.classList.add('offline');
                        statusElement.className = 'status-indicator status-grey';
                        numberElement.textContent = 'Offline';
                    }
                });
        });
    </script>
</body>
</html>
