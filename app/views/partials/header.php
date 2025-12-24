<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <a href="/casjoe-pay">Pay</a>
                <a href="/erp">ERP</a>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">