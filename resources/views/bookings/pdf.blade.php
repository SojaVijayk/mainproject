<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation - {{ $booking->booking_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin-bottom: 0; }
        .header p { margin-top: 0; color: #666; }
        .section { margin-bottom: 20px; }
        .section h2 { border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Booking Confirmation</h1>
        <p>Booking Number: {{ $booking->booking_number }}</p>
    </div>

    <div class="section">
        <h2>Event Details</h2>
        <table>
            <tr>
                <th>Event Name</th>
                <td>{{ $booking->event_name }}</td>
            </tr>
            <tr>
                <th>Event Type</th>
                <td>{{ ucfirst($booking->event_type) }}</td>
            </tr>
            <tr>
                <th>Event Mode</th>
                <td>{{ ucfirst($booking->event_mode) }}</td>
            </tr>
            <tr>
                <th>Hosted By</th>
                <td>{{ $booking->hostedBy->name }}</td>
            </tr>
            @if($booking->external_organization)
            <tr>
                <th>External Organization</th>
                <td>{{ $booking->external_organization }}</td>
            </tr>
            @endif
            <tr>
                <th>Coordinator</th>
                <td>{{ $booking->coordinator->name }}</td>
            </tr>
            <tr>
                <th>Participants</th>
                <td>{{ $booking->participants_count }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Date & Time</h2>
        <table>
            <tr>
                <th>From</th>
                <td>{{ $booking->start_time->format('M d, Y h:i A') }}</td>
            </tr>
            <tr>
                <th>To</th>
                <td>{{ $booking->end_time->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Venues</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Capacity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->venues as $venue)
                <tr>
                    <td>{{ $venue->name }}</td>
                    <td>{{ $venue->seating_capacity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Setup Details</h2>
        <table>
            <tr>
                <th>Seat Layout</th>
                <td>
                    {{ str_replace('_', ' ', ucfirst($booking->seat_layout)) }}
                    @if($booking->custom_seat_layout)
                        ({{ $booking->custom_seat_layout }})
                    @endif
                </td>
            </tr>
            <tr>
                <th>Amenities</th>
                <td>
                    @if($booking->amenities)
                        {{ implode(', ', $booking->amenities) }}
                    @else
                        None
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if($booking->additional_requirements)
    <div class="section">
        <h2>Additional Requirements</h2>
        <p>{{ $booking->additional_requirements }}</p>
    </div>
    @endif

    <div class="footer">
        <p>This is an automatically generated booking confirmation. Please contact the coordinator if you have any questions.</p>
    </div>
</body>
</html>