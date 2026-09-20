<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - You Miss Road | Casjoe</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        :root {
            --primary: #000066;
            --accent: #FFA600;
            --background: #050510;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Ambient background blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 0, 102, 0.3) 0%, rgba(5, 5, 16, 0) 70%);
            filter: blur(80px);
            z-index: -1;
            animation: move 20s infinite alternate;
        }

        .blob-1 { top: -100px; left: -100px; background: radial-gradient(circle, rgba(255, 166, 0, 0.15) 0%, rgba(5, 5, 16, 0) 70%); }
        .blob-2 { bottom: -100px; right: -100px; background: radial-gradient(circle, rgba(0, 0, 102, 0.4) 0%, rgba(5, 5, 16, 0) 70%); }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }

        /* Glass Container */
        .container {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 60px;
            border-radius: 40px;
            text-align: center;
            max-width: 600px;
            width: 90%;
            position: relative;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .error-code {
            font-size: 120px;
            font-weight: 700;
            background: linear-gradient(135deg, white 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background-color: var(--accent);
            color: var(--primary);
        }

        .btn-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 166, 0, 0.3);
            filter: brightness(1.1);
        }

        .btn-outline {
            border: 1px solid var(--glass-border);
            color: white;
            background: rgba(255, 255, 255, 0.05);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }

        .lost-icon {
            font-size: 80px;
            color: var(--accent);
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Tag for flavor */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(255, 166, 0, 0.1);
            color: var(--accent);
            border-radius: 100px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="container">
        <div class="badge">Connection lost</div>
        <div class="lost-icon">
            <ion-icon name="navigate-circle-outline"></ion-icon>
        </div>
        <div class="error-code">404</div>
        <h1>Ahh, you just miss road! 🚀</h1>
        <p>The page you're looking for has moved to a hidden corner of the internet. No shaking, let's get you back on track.</p>
        
        <div class="actions">
            <a href="/" class="btn btn-primary">
                <ion-icon name="home-outline"></ion-icon>
                Go Home
            </a>
            <a href="javascript:history.back()" class="btn btn-outline">
                <ion-icon name="arrow-back-outline"></ion-icon>
                Go Back
            </a>
        </div>
    </div>
</body>
</html>
