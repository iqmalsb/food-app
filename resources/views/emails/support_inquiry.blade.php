<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support Inquiry Received</title>
    <style>
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f6f7ff;
            color: #1b1b2f;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e0e2f9;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.05);
        }
        .header {
            background-color: #6366f1;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .content {
            padding: 30px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .meta-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f0f1fa;
        }
        .meta-table td.label {
            font-weight: 600;
            color: #555670;
            width: 35%;
        }
        .meta-table td.value {
            color: #1b1b2f;
        }
        .message-box {
            background-color: #f6f7ff;
            border-left: 4px solid #6366f1;
            padding: 20px;
            border-radius: 4px;
            white-space: pre-wrap;
            font-size: 0.95rem;
            line-height: 1.6;
            color: #33344a;
        }
        .footer {
            background-color: #f9f9fb;
            padding: 20px;
            text-align: center;
            font-size: 0.8rem;
            color: #88899c;
            border-top: 1px solid #f0f1fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Support Inquiry</h2>
        </div>
        <div class="content">
            <table class="meta-table">
                <tr>
                    <td class="label">Sender Name:</td>
                    <td class="value">{{ $user->name }}</td>
                </tr>
                <tr>
                    <td class="label">Sender Email:</td>
                    <td class="value">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td class="label">Sender Role:</td>
                    <td class="value">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td>
                </tr>
                <tr>
                    <td class="label">Organisation:</td>
                    <td class="value">{{ $user->organisation ? $user->organisation->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Inquiry Type:</td>
                    <td class="value"><strong>{{ ucfirst(str_replace('_', ' ', $inquiryType)) }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Subject:</td>
                    <td class="value">{{ $subject }}</td>
                </tr>
            </table>

            <h3 style="margin-top: 0; color: #1b1b2f; font-size: 1.1rem; border-bottom: 2px solid #6366f1; padding-bottom: 6px; display: inline-block;">
                Message Details
            </h3>
            <div class="message-box">{{ $messageContent }}</div>
        </div>
        <div class="footer">
            Sent from DineFlow Support Portal • {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
