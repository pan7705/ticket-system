<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Queue Ticketing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .counter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .counter {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            width: calc(25% - 20px);
            min-width: 200px;
            text-align: center;
        }
        .counter.offline {
            opacity: 0.5;
            background-color: #f5f5f5;
        }
        .status-indicator {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
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
            border-radius: 5px;
        }
        .section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .section-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .counter-btn {
            width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Queue Ticketing System</h1>

        <div class="row">
            <!-- Customer View Section -->
            <div class="col-md-12 section">
                <h2 class="section-title">Customer View</h2>

                <div class="info-panel">
                    <h3>Now Serving: <span id="now-serving">{{ $nowServingNumber }}</span></h3>
                    <h3>Last Number: <span id="last-number">{{ $lastTicketNumber }}</span></h3>
                    <button class="btn btn-success btn-lg mt-3" id="take-number-btn">Take a Number</button>
                </div>

                <div class="counter-container">
                    @foreach($counters as $counter)
                    <div class="counter {{ !$counter->is_online ? 'offline' : '' }}" id="customer-counter-{{ $counter->id }}">
                        <h4>Counter {{ $counter->id }}</h4>
                        <div>
                            <span class="status-indicator {{ $counter->is_online ? ($counter->is_serving ? 'status-red' : 'status-green') : 'status-grey' }}" id="customer-status-{{ $counter->id }}"></span>
                            <span id="customer-counter-number-{{ $counter->id }}">{{ $counter->is_online ? ($counter->current_ticket_number ?? 'Available') : 'Offline' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Counter Management Section -->
            <div class="col-md-12 section">
                <h2 class="section-title">Counter Management</h2>

                <div class="counter-container">
                    @foreach($counters as $counter)
                    <div class="counter" id="management-counter-{{ $counter->id }}">
                        <h4>Counter {{ $counter->id }}</h4>
                        <button class="btn {{ $counter->is_online ? 'btn-danger' : 'btn-success' }} counter-btn toggle-status-btn"
                                data-counter-id="{{ $counter->id }}">
                            {{ $counter->is_online ? 'Go Offline' : 'Go Online' }}
                        </button>

                        <button class="btn btn-primary counter-btn complete-current-btn"
                                data-counter-id="{{ $counter->id }}"
                                {{ !$counter->is_online || !$counter->is_serving ? 'disabled' : '' }}>
                            Complete Current
                        </button>

                        <button class="btn btn-info counter-btn call-next-btn"
                                data-counter-id="{{ $counter->id }}"
                                {{ !$counter->is_online || $counter->is_serving ? 'disabled' : '' }}>
                            Call Next
                        </button>

                        <div class="mt-2" id="management-counter-number-{{ $counter->id }}">
                            Current: {{ $counter->current_ticket_number ?? 'None' }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Take a number
            $('#take-number-btn').click(function() {
                $.ajax({
                    url: '/take-number',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#last-number').text(response.last_number);
                            alert('Your ticket number is: ' + response.ticket_number);
                        }
                    }
                });
            });

            // Toggle counter status
            $('.toggle-status-btn').click(function() {
                const counterId = $(this).data('counter-id');

                $.ajax({
                    url: `/toggle-status/${counterId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCounterStatus(response.counter);
                        }
                    }
                });
            });

            // Complete current ticket
            $('.complete-current-btn').click(function() {
                const counterId = $(this).data('counter-id');

                $.ajax({
                    url: `/complete-current/${counterId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCounterStatus(response.counter);
                            $('#now-serving').text(response.now_serving);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });

            // Call next ticket
            $('.call-next-btn').click(function() {
                const counterId = $(this).data('counter-id');

                $.ajax({
                    url: `/call-next/${counterId}`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            updateCounterStatus(response.counter);
                            $('#now-serving').text(response.now_serving);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });

            // Function to update counter status in both views
            function updateCounterStatus(counter) {
                // Update customer view
                const customerCounter = $(`#customer-counter-${counter.id}`);
                const customerStatus = $(`#customer-status-${counter.id}`);
                const customerNumber = $(`#customer-counter-number-${counter.id}`);

                // Update management view
                const managementCounter = $(`#management-counter-${counter.id}`);
                const toggleButton = managementCounter.find('.toggle-status-btn');
                const completeButton = managementCounter.find('.complete-current-btn');
                const callNextButton = managementCounter.find('.call-next-btn');
                const managementNumber = $(`#management-counter-number-${counter.id}`);

                if (counter.is_online) {
                    // Counter is online
                    customerCounter.removeClass('offline');
                    toggleButton.removeClass('btn-success').addClass('btn-danger');
                    toggleButton.text('Go Offline');

                    if (counter.is_serving) {
                        // Counter is serving
                        customerStatus.removeClass('status-green status-grey').addClass('status-red');
                        customerNumber.text(counter.current_ticket_number);

                        completeButton.prop('disabled', false);
                        callNextButton.prop('disabled', true);
                        managementNumber.text(`Current: ${counter.current_ticket_number}`);
                    } else {
                        // Counter is available
                        customerStatus.removeClass('status-red status-grey').addClass('status-green');
                        customerNumber.text('Available');

                        completeButton.prop('disabled', true);
                        callNextButton.prop('disabled', false);
                        managementNumber.text('Current: None');
                    }
                } else {
                    // Counter is offline
                    customerCounter.addClass('offline');
                    customerStatus.removeClass('status-green status-red').addClass('status-grey');
                    customerNumber.text('Offline');

                    toggleButton.removeClass('btn-danger').addClass('btn-success');
                    toggleButton.text('Go Online');
                    completeButton.prop('disabled', true);
                    callNextButton.prop('disabled', true);
                    managementNumber.text('Current: None');
                }
            }
        });
    </script>
</body>
</html>
