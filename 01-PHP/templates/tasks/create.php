<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body p-4">
                <h1 class="card-title mb-4">Nueva Tarea</h1>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= App\Security::escape($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="/tasks/create">
                    <?= App\Security::csrfField() ?>
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="200">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Fecha Límite</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="/tasks" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
