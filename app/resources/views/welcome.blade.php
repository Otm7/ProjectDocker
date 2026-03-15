<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <style>
        body { font-family: system-ui, sans-serif; text-align: center; padding: 2rem; }
        .links > a { margin: 0 0.5rem; text-decoration: none; color: #636b6f; }
        .esgi-server-label { font-weight: bold; color: #2c3e50; }
        .form-box { display: inline-block; text-align: left; border: 1px solid #ccc; padding: 1rem 1.5rem; border-radius: 8px; margin-top: 1rem; }
        .form-box input { width: 100%; padding: 0.4rem; margin: 0.3rem 0; }
        .form-box button { width: 100%; padding: 0.5rem; margin-top: 0.5rem; }
        .notice { color: #2a7; margin-bottom: 1rem; }
        .error { color: #c33; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="content">
        <div class="title m-b-md">
            <span class="esgi-server-label">{{ $_SERVER['SERVER_LABEL'] ?? 'Serveur' }}</span>
        </div>

        {{ AUTH_CONTENT }}

    </div>
</body>
</html>
