<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Activated</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
        }

        .success-icon {
            font-size: 3rem;
            color: #28a745;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="success-icon mb-3">
        <i class="fa-regular fa-circle-check"></i>
    </div>
    <h2 class="mb-2">Account Activated!</h2>
    <p class="text-muted mb-4">Your email has been successfully verified. You can now access all features of your account.</p>
    <a href="/home" class="btn btn-success">Go to your Pernote homepage</a>
</div>

</body>
</html>
