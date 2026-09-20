<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/js/casjoe_theme.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/favicon.png">
    <title>Casjoe App</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <header>
        <div class="logo">Casjoe</div>
        <nav>
            <a href="/">Home</a>
            <?php if (\App\Core\Auth::user()): ?>
                <a href="/academy">Academy</a>
                <a href="/pay">Pay</a>
                <a href="/erp">ERP</a>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
