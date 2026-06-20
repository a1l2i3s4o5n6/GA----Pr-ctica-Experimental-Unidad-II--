<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="card-title"><?= App\Security::escape($task['title']) ?></h1>
                    <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?> fs-6">
                        <?= App\Security::escape(ucfirst(str_replace('_', ' ', $task['status']))) ?>
                    </span>
                </div>

                <hr>

                <div class="mb-4">
                    <h5>Descripción</h5>
                    <p><?= $task['description'] ? nl2br(App\Security::escape($task['description'])) : 'Sin descripción.' ?></p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <strong>Fecha Límite:</strong>
                        <?= $task['due_date'] ? App\Security::escape($task['due_date']) : 'No definida' ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Creada:</strong>
                        <?= App\Security::escape(date('d/m/Y H:i', strtotime($task['created_at']))) ?>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="/tasks/<?= (int)$task['id'] ?>/edit" class="btn btn-warning">Editar</a>
                    <form method="POST" action="/tasks/<?= (int)$task['id'] ?>/delete" class="d-inline">
                        <?= App\Security::csrfField() ?>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</button>
                    </form>
                    <a href="/tasks" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </div>
</div>
