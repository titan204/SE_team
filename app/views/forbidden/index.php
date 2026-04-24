<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container d-flex justify-content-center align-items-center vh-100">

    <div class="text-center">

        <h1 class="display-1 fw-bold text-primary">404 - forbidden</h1>

        <h2 class="mb-3">Page Not Found</h2>

        <p class="mb-4 text-secondary">
            The page you are looking for does not exist or has been moved.
        </p>

        <a href="<?= isset($baseUrl) ? $baseUrl : '/' ?>" class="btn btn-primary px-4 py-2">
            Go Home
        </a>

    </div>

</div>

</body>
</html>