<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Queue Ticketing System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --info-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --purple-color: #7209b7;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #333;
            padding: 0;
            margin: 0;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 1200px;
        }

        .section-title {
            position: relative;
            padding-left: 15px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 5px;
        }

        .info-panel {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .info-number {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0;
        }

        .info-label {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .counter-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .counter {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .counter.offline {
            opacity: 0.7;
            background-color: #f8f9fa;
        }

        .counter-header {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .counter-content {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-green {
            background-color: #4ade80;
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .status-red {
            background-color: #f43f5e;
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.2);
        }

        .status-grey {
            background-color: #94a3b8;
            box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.2);
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-take-number {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
            transition: all 0.3s ease;
        }

        .btn-take-number:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        .counter-btn {
            width: 100%;
            margin-bottom: 10px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .counter-btn i {
            margin-right: 8px;
        }

        .btn-offline {
            background-color: #f43f5e;
            border: none;
            color: white;
        }

        .btn-online {
            background-color: #4ade80;
            border: none;
            color: white;
        }

        .btn-complete {
            background-color: #e0e0e0;
            border: none;
            color: #333;
        }

        .btn-complete:not(:disabled) {
            background-color: #0ea5e9;
            color: white;
        }

        .btn-next {
            background-color: #e0e0e0;
            border: none;
            color: #333;
        }

        .btn-next:not(:disabled) {
            background-color: var(--purple-color);
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .current-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.5;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
            background-color: var(--primary-color);
            color: white;
        }

        .nav-tabs {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            border-color: rgba(67, 97, 238, 0.3);
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: transparent;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0;
            border-bottom: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
        }

        .ticket-number {
            font-size: 4rem;
            font-weight: 700;
            color: var(--primary-color);
            text-align: center;
            margin: 1.5rem 0;
        }

        .ticket-info {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #6c757d;
        }

        .btn-close-modal {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-close-modal:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .alert-modal .modal-header {
            background: #f43f5e;
        }

        .alert-modal .modal-body {
            padding: 1.5rem;
            text-align: center;
        }

        .alert-message {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 576px) {
            .counter-container {
                grid-template-columns: repeat(1, 1fr);
            }

            .nav-tabs .nav-link {
                padding: 0.7rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <h1 class="text-center mb-0">
                <i class="fas fa-ticket-alt me-2"></i>Queue Ticketing System
            </h1>
        </div>
    </div>

    <div class="container">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab" aria-controls="customer" aria-selected="true">
                    <i class="fas fa-users me-2"></i>Customer View
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="management-tab" data-bs-toggle="tab" data-bs-target="#management" type="button" role="tab" aria-controls="management" aria-selected="false">
                    <i class="fas fa-cogs me-2"></i>Counter Management
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="myTabContent">
            <!-- Customer View Tab -->
            <div class="tab-pane fade show active" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                <h3 class="section-title">Customer Information</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-panel">
                            <p class="info-label">Now Serving</p>
                            <h2 class="info-number" id="now-serving">{{ $nowServingNumber }}</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-panel">
                            <p class="info-label">Last Number</p>
                            <h2 class="info-number" id="last-number">{{ $lastTicketNumber }}</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-panel d-flex align-items-center justify-content-center">
                            <button class="btn btn-take-number" id="take-number-btn">
                                <i class="fas fa-plus-circle me-2"></i>Take a Number
                            </button>
                        </div>
                    </div>
                </div>

                <h3 class="section-title mt-4">Counter Status</h3>

                <div class="counter-container">
                    @foreach($counters as $counter)
                    <div class="counter {{ !$counter->is_online ? 'offline' : '' }}" id="customer-counter-{{ $counter->id }}">
                        <div class="counter-header">
                            <i class="fas fa-desktop me-2"></i>Counter {{ $counter->id }}
                        </div>
                        <div class="counter-content">
                            <span class="status-indicator {{ $counter->is_online ? ($counter->is_serving ? 'status-red' : 'status-green') : 'status-grey' }}" id="customer-status-{{ $counter->id }}"></span>
                            <span id="customer-counter-number-{{ $counter->id }}">{{ $counter->is_online ? ($counter->current_ticket_number ?? 'Available') : 'Offline' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Counter Management Tab -->
            <div class="tab-pane fade" id="management" role="tabpanel" aria-labelledby="management-tab">
                <h3 class="section-title">Counter Controls</h3>

                <div class="counter-container">
                    @foreach($counters as $counter)
                    <div class="counter" id="management-counter-{{ $counter->id }}">
                        <div class="counter-header">
                            <i class="fas fa-desktop me-2"></i>Counter {{ $counter->id }}
                        </div>

                        <button class="btn {{ $counter->is_online ? 'btn-offline' : 'btn-online' }} counter-btn toggle-status-btn"
                                data-counter-id="{{ $counter->id }}">
                            <i class="fas fa-power-off"></i>
                            {{ $counter->is_online ? 'Go Offline' : 'Go Online' }}
                        </button>

                        <button class="btn btn-complete counter-btn complete-current-btn"
                                data-counter-id="{{ $counter->id }}"
                                {{ !$counter->is_online || !$counter->is_serving ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle"></i>Complete Current
                        </button>

                        <button class="btn btn-next counter-btn call-next-btn"
                                data-counter-id="{{ $counter->id }}"
                                {{ !$counter->is_online || $counter->is_serving ? 'disabled' : '' }}>
                            <i class="fas fa-arrow-right"></i>Call Next
                        </button>

                        <div class="mt-3 text-center">
                            <span class="current-badge" id="management-counter-number-{{ $counter->id }}">
                                Current: {{ $counter->current_ticket_number ?? 'None' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Your Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="ticket-info">Please keep this number and wait until it's called</div>
                    <div class="ticket-number" id="modal-ticket-number">0</div>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div class="modal fade alert-modal" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert-message" id="alert-message"></div>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-close-modal" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));
            const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));

            // Function to show alert modal
            function showAlert(message) {
                $('#alert-message').text(message);
                alertModal.show();
            }

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
                            $('#modal-ticket-number').text(response.ticket_number);
                            ticketModal.show();
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
                            showAlert(response.message);
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
                            showAlert(response.message);
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
                    toggleButton.removeClass('btn-online').addClass('btn-offline');
                    toggleButton.html('<i class="fas fa-power-off"></i> Go Offline');

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

                    toggleButton.removeClass('btn-offline').addClass('btn-online');
                    toggleButton.html('<i class="fas fa-power-off"></i> Go Online');
                    completeButton.prop('disabled', true);
                    callNextButton.prop('disabled', true);
                    managementNumber.text('Current: None');
                }
            }

            // Add hover effects to counters
            $('.counter').hover(
                function() {
                    $(this).css('transform', 'translateY(-5px)');
                    $(this).css('box-shadow', '0 10px 20px rgba(0, 0, 0, 0.1)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                    $(this).css('box-shadow', '0 5px 15px rgba(0, 0, 0, 0.05)');
                }
            );
        });
    </script>
</body>
</html>
