<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= App\Security::escape($title ?? 'Gestión de Tareas') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Gestión de Tareas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (App\Auth::isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/tasks">Mis Tareas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/tasks/create">Nueva Tarea</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link text-light">
                                <?= App\Security::escape($_SESSION['username'] ?? '') ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="/logout" class="d-inline">
                                <?= App\Security::csrfField() ?>
                                <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
