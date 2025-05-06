<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Queue Ticketing - Counter Management</title>
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
        .counter-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .counter-btn.offline {
            background-color: #f44336;
        }
        .counter-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <h1 class="text-center">Counter Management</h1>

    <div class="counter-container">
        @foreach($counters as $counter)
        <div class="counter" id="counter-{{ $counter->id }}">
            <h3>Counter {{ $counter->id }}</h3>
            <button class="counter-btn {{ $counter->is_online ? '' : 'offline' }}" id="toggle-status-{{ $counter->id }}" data-counter-id="{{ $counter->id }}">
                {{ $counter->is_online ? 'Go Offline' : 'Go Online' }}
            </button>
            <button class="counter-btn" id="complete-current-{{ $counter->id }}" data-counter-id="{{ $counter->id }}" {{ !$counter->is_online || !$counter->is_serving ? 'disabled' : '' }}>
                Complete Current
            </button>
            <button class="counter-btn" id="call-next-{{ $counter->id }}" data-counter-id="{{ $counter->id }}" {{ !$counter->is_online || $counter->is_serving ? 'disabled' : '' }}>
                Call Next
            </button>
            <div id="current-ticket-{{ $counter->id }}">
                Current Ticket: {{ $counter->current_ticket_number ?? 'None' }}
            </div>
        </div>
        @endforeach
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Toggle counter status
            document.querySelectorAll('[id^="toggle-status-"]').forEach(button => {
                button.addEventListener('click', function() {
                    const counterId = this.getAttribute('data-counter-id');

                    fetch(`/counter/${counterId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'online') {
                            this.textContent = 'Go Offline';
                            this.classList.remove('offline');
                            document.getElementById(`call-next-${counterId}`).removeAttribute('disabled');
                        } else {
                            this.textContent = 'Go Online';
                            this.classList.add('offline');
                            document.getElementById(`complete-current-${counterId}`).setAttribute('disabled', true);
                            document.getElementById(`call-next-${counterId}`).setAttribute('disabled', true);
                            document.getElementById(`current-ticket-${counterId}`).textContent = 'Current Ticket: None';
                        }
                    });
                });
            });

            // Complete current ticket
            document.querySelectorAll('[id^="complete-current-"]').forEach(button => {
                button.addEventListener('click', function() {
                    const counterId = this.getAttribute('data-counter-id');

                    fetch(`/counter/${counterId}/complete-current`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.setAttribute('disabled', true);
                            document.getElementById(`call-next-${counterId}`).removeAttribute('disabled');
                            document.getElementById(`current-ticket-${counterId}`).textContent = 'Current Ticket: None';
                        } else {
                            alert(data.message);
                        }
                    });
                });
            });

            // Call next ticket
            document.querySelectorAll('[id^="call-next-"]').forEach(button => {
                button.addEventListener('click', function() {
                    const counterId = this.getAttribute('data-counter-id');

                    fetch(`/counter/${counterId}/call-next`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.setAttribute('disabled', true);
                            document.getElementById(`complete-current-${counterId}`).removeAttribute('disabled');
                            document.getElementById(`current-ticket-${counterId}`).textContent = `Current Ticket: ${data.ticket_number}`;
                        } else {
                            alert(data.message);
                        }
                    });
                });
            });

            // Real-time updates with Laravel Echo
            window.Echo.channel('counters')
                .listen('CounterStatusChanged', (e) => {
                    const counterId = e.counter.id;
                    const toggleButton = document.getElementById(`toggle-status-${counterId}`);
                    const completeButton = document.getElementById(`complete-current-${counterId}`);
                    const callNextButton = document.getElementById(`call-next-${counterId}`);
                    const currentTicketElement = document.getElementById(`current-ticket-${counterId}`);

                    if (e.counter.is_online) {
                        toggleButton.textContent = 'Go Offline';
                        toggleButton.classList.remove('offline');

                        if (e.counter.is_serving) {
                            completeButton.removeAttribute('disabled');
                            callNextButton.setAttribute('disabled', true);
                            currentTicketElement.textContent = `Current Ticket: ${e.counter.current_ticket_number}`;
                        } else {
                            completeButton.setAttribute('disabled', true);
                            callNextButton.removeAttribute('disabled');
                            currentTicketElement.textContent = 'Current Ticket: None';
                        }
                    } else {
                        toggleButton.textContent = 'Go Online';
                        toggleButton.classList.add('offline');
                        completeButton.setAttribute('disabled', true);
                        callNextButton.setAttribute('disabled', true);
                        currentTicketElement.textContent = 'Current Ticket: None';
                    }
                });
        });
    </script>
</body>
</html>
