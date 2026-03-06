<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ system_setting('site_name') }} - {{ trans('front/maintenance.title') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .container {
                text-align: center;
                padding: 40px 20px;
            }
        h1 {
                font-size: 3rem;
                margin-bottom: 1rem;
                opacity: 0;
                animation: fadeIn 1s ease-in forwards;
            }
        p {
                font-size: 1.2rem;
                opacity: 0;
                animation: fadeIn 1s ease-in forwards;
                animation-delay: 0.3s;
            }
        .icon {
                font-size: 5rem;
                margin-bottom: 2rem;
                opacity: 0;
                animation: pulse 2s infinite;
            }
        @keyframes fadeIn {
                to { opacity: 1; }
            from { opacity: 0; }
        }
        @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>{{ trans('front/maintenance.title') }}</h1>
        <p>{{ trans('front/maintenance.message') }}</p>
    </div>
</body>
</html>
