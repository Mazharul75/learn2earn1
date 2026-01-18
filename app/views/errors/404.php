<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #f8f9fa; }
        h1 { color: #e74c3c; font-size: 3rem; margin-bottom: 10px; }
        p { color: #666; font-size: 1.2rem; }
        a { text-decoration: none; background: #3498db; color: white; padding: 10px 20px; border-radius: 5px; }
        a:hover { background: #2980b9; }
    </style>
</head>
<body>
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>The page you are looking for does not exist or has been moved.</p>
    <br>
    <a href="<?= BASE_URL ?>dashboard/index">Go Back Home</a>
</body>
</html>