<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="/js/casjoe_theme.js"></script>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found | Casjoe</title>
    <link rel="stylesheet" href="/css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0f13 0%, #000000 100%);
            font-family: 'Outfit', sans-serif;
            color: #ffffff;
            overflow: hidden;
            position: relative;
        }

        /* Abstract glowing orbs in background */
        .glow-orb-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 166, 0, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            z-index: 0;
        }
        
        .glow-orb-2 {
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 0, 102, 0.2) 0%, rgba(0, 0, 0, 0) 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .error-container {
            position: relative;
            z-index: 10;
            background: rgba(30, 30, 30, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            padding: 60px 50px;
            text-align: center;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: floatUp 1s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes floatUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-wrap {
            margin-bottom: 30px;
        }

        .logo-wrap img {
            height: 50px;
        }

        .error-code {
            font-size: 10rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(180deg, #ffffff 0%, rgba(255, 255, 255, 0.2) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            letter-spacing: -5px;
            position: relative;
            display: inline-block;
        }

        .error-code::after {
            content: '404';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, var(--secondary) 0%, transparent 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0.5;
            filter: blur(15px);
            z-index: -1;
        }

        .error-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 20px 0 15px;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .error-title span {
            color: var(--secondary);
        }

        .error-msg {
            color: #aaaaaa;
            font-size: 1.15rem;
            line-height: 1.6;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--secondary);
            color: var(--primary-dark);
            padding: 16px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 166, 0, 0.4);
            background: var(--secondary-hover);
        }

        .btn-outline {
            background: transparent;
            color: #ffffff;
            padding: 16px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-3px);
        }

        /* Glitch effect on hover for fun */
        .error-code:hover {
            animation: glitch 0.3s infinite;
        }

        @keyframes glitch {
            0% { transform: translate(0) }
            20% { transform: translate(-2px, 2px) }
            40% { transform: translate(-2px, -2px) }
            60% { transform: translate(2px, 2px) }
            80% { transform: translate(2px, -2px) }
            100% { transform: translate(0) }
        }

        @media (max-width: 600px) {
            .error-code { font-size: 6rem; }
            .error-title { font-size: 1.8rem; }
            .action-buttons { flex-direction: column; }
            .error-container { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <div class="glow-orb-1"></div>
    <div class="glow-orb-2"></div>

    <div class="error-container">
        <div class="logo-wrap">
            <a href="/"><img src="/assets/casjoe_logo.png" alt="Casjoe"></a>
        </div>
        
        <div class="error-code">404</div>
        <div class="error-title">Ahh... <span>You don miss road!</span> 🚧</div>
        <div class="error-msg">The page you are looking for has been moved, deleted, or possibly never existed. Let's get you back on track.</div>
        
        <div class="action-buttons">
            <a href="/" class="btn-primary">
                <ion-icon name="home"></ion-icon> Home
            </a>
            <a href="javascript:history.back()" class="btn-outline">
                <ion-icon name="arrow-back"></ion-icon> Go Back
            </a>
        </div>
    </div>
</body>
</html>
