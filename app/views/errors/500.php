<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #fff0f0; }
        h1 { color: #c0392b; font-size: 3rem; margin-bottom: 10px; }
        p { color: #444; font-size: 1.2rem; }
        .error-box { background: #fff; border: 1px solid #fab1a0; padding: 20px; display: inline-block; margin-top: 20px; text-align: left; }
    </style>
</head>
<body>
    <h1>500</h1>
    <h2>Internal Server Error</h2>
    <p>Something went wrong on our end. Please try again later.</p>
    
    <?php if (isset($error_message)): ?>
        <div class="error-box">
            <strong>Error Details:</strong><br>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>
</body>
</html>