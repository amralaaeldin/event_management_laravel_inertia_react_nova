<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Event Join Notification</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333333;
        }

        .content {
            padding: 20px 0;
        }

        .content h2 {
            font-size: 20px;
            color: #444444;
            margin-bottom: 10px;
        }

        .content p {
            font-size: 16px;
            color: #666666;
            margin: 0 0 15px;
        }

        .content .event-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #eeeeee;
            margin-top: 20px;
        }

        .content .event-details p {
            margin: 0;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 14px;
            color: #999999;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>You've Joined a New Event!</h1>
        </div>

        <div class="content">
            <h2>Hello, {{ $user->name }}!</h2>
            <p>You have successfully joined the event <strong>{{ $event->name }}</strong>. Below are the details:</p>

            <div class="event-details">
                <p><strong>Event Name:</strong> {{ $event->name }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_date_time)->format('F j, Y') }}</p>
                <p><strong>Time:</strong>
                    {{ \Carbon\Carbon::parse($event->start_date_time)->format('h:i A') }} -
                    {{ \Carbon\Carbon::parse($event->start_date_time)->addMinutes($event->duration)->format('h:i A') }}
                </p>
                <p><strong>Location:</strong> {{ $event->location }}</p>
                <p><strong>Description:</strong> {{ $event->description }}</p>
            </div>

            <p>We look forward to seeing you there!</p>
        </div>

        <div class="footer">
            <p>If you have any questions, feel free to <a href="mailto:support@example.com">contact us</a>.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
