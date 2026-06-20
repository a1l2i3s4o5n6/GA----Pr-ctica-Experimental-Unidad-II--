<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body p-4">
                <h1 class="card-title">Bienvenido, <?= App\Security::escape($_SESSION['username'] ?? '') ?></h1>
                <p class="card-text">Panel de control de la aplicación de gestión de tareas.</p>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-bg-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= App\Security::escape((string)$totalTasks) ?></h5>
                                <p class="card-text">Total Tareas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-bg-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= App\Security::escape((string)$pendingTasks) ?></h5>
                                <p class="card-text">Pendientes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card text-bg-success">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= App\Security::escape((string)$completedTasks) ?></h5>
                                <p class="card-text">Completadas</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="/tasks" class="btn btn-primary">Ver Mis Tareas</a>
                    <a href="/tasks/create" class="btn btn-success">Nueva Tarea</a>
                </div>
            </div>
        </div>
    </div>
</div>
