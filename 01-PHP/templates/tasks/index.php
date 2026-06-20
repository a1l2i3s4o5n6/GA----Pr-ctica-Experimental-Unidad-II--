<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Mis Tareas</h1>
    <a href="/tasks/create" class="btn btn-success">+ Nueva Tarea</a>
</div>

<?php if (empty($tasks)): ?>
    <div class="alert alert-info">
        No tienes tareas registradas.
        <a href="/tasks/create" class="alert-link">Crea tu primera tarea</a>.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Título</th>
                    <th>Estado</th>
                    <th>Fecha Límite</th>
                    <th>Creada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?= App\Security::escape($task['title']) ?></td>
                        <td>
                            <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
                                <?= App\Security::escape(ucfirst(str_replace('_', ' ', $task['status']))) ?>
                            </span>
                        </td>
                        <td><?= $task['due_date'] ? App\Security::escape($task['due_date']) : '—' ?></td>
                        <td><?= App\Security::escape(date('d/m/Y', strtotime($task['created_at']))) ?></td>
                        <td>
                            <a href="/tasks/<?= (int)$task['id'] ?>" class="btn btn-sm btn-info">Ver</a>
                            <a href="/tasks/<?= (int)$task['id'] ?>/edit" class="btn btn-sm btn-warning">Editar</a>
                            <form method="POST" action="/tasks/<?= (int)$task['id'] ?>/delete" class="d-inline">
                                <?= App\Security::csrfField() ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
