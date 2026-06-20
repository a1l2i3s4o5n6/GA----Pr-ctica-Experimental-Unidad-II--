
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Auth;
use App\Security;
use App\Task;
use App\Database;
session_start();
Security::sendSecurityHeaders();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$task = new Task();
try {
    switch (true) {
        // === AUTH ROUTES ===
        case $uri === '/login' && $method === 'GET':
            $title = 'Iniciar Sesión';
            ob_start();
            include __DIR__ . '/../templates/auth/login.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;
        case $uri === '/login' && $method === 'POST':
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Error de validación CSRF.';
                $title = 'Iniciar Sesión';
                ob_start();
                include __DIR__ . '/../templates/auth/login.php';
                $content = ob_get_clean();
                include __DIR__ . '/../templates/layout/header.php';
                echo $content;
                include __DIR__ . '/../templates/layout/footer.php';
                break;
            }
            $auth = new Auth();
            $result = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($result['success']) {
                header('Location: /');
                exit;
            }
            $error = $result['error'];
            $title = 'Iniciar Sesión';
            ob_start();
            include __DIR__ . '/../templates/auth/login.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case $uri === '/register' && $method === 'GET':
            $title = 'Registro';
            ob_start();
            include __DIR__ . '/../templates/auth/register.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case $uri === '/register' && $method === 'POST':
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Error de validación CSRF.';
                $title = 'Registro';
                ob_start();
                include __DIR__ . '/../templates/auth/register.php';
                $content = ob_get_clean();
                include __DIR__ . '/../templates/layout/header.php';
                echo $content;
                include __DIR__ . '/../templates/layout/footer.php';
                break;
            }
            if (($_POST['password'] ?? '') !== ($_POST['password_confirm'] ?? '')) {
                $error = 'Las contraseñas no coinciden.';
                $title = 'Registro';
                ob_start();
                include __DIR__ . '/../templates/auth/register.php';
                $content = ob_get_clean();
                include __DIR__ . '/../templates/layout/header.php';
                echo $content;
                include __DIR__ . '/../templates/layout/footer.php';
                break;
            }
            $auth = new Auth();
            $result = $auth->register(
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['password'] ?? ''
            );
            if ($result['success']) {
                header('Location: /login?registered=1');
                exit;
            }
            $error = $result['error'];
            $title = 'Registro';
            ob_start();
            include __DIR__ . '/../templates/auth/register.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case $uri === '/logout' && $method === 'POST':
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                http_response_code(403);
                die('Error CSRF');
            }
            $auth = new Auth();
            $auth->logout();
            header('Location: /login');
            exit;

        case $uri === '/' || $uri === '/dashboard':
            Auth::requireLogin();
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT COUNT(*) FROM tasks WHERE user_id = :uid');
            $stmt->execute(['uid' => $_SESSION['user_id']]);
            $totalTasks = (int)$stmt->fetchColumn();

            $stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND status = 'pending'");
            $stmt->execute(['uid' => $_SESSION['user_id']]);
            $pendingTasks = (int)$stmt->fetchColumn();

            $stmt = $db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = :uid AND status = 'completed'");
            $stmt->execute(['uid' => $_SESSION['user_id']]);
            $completedTasks = (int)$stmt->fetchColumn();

            $title = 'Dashboard';
            ob_start();
            include __DIR__ . '/../templates/auth/dashboard.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case $uri === '/tasks' && $method === 'GET':
            Auth::requireLogin();
            $tasks = $task->getAll($_SESSION['user_id']);
            $title = 'Mis Tareas';
            ob_start();
            include __DIR__ . '/../templates/tasks/index.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case $uri === '/tasks/create' && $method === 'GET':
            Auth::requireLogin();
            $title = 'Nueva Tarea';
            ob_start();
            include __DIR__ . '/../templates/tasks/create.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case $uri === '/tasks/create' && $method === 'POST':
            Auth::requireLogin();
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Error de validación CSRF.';
                $title = 'Nueva Tarea';
                ob_start();
                include __DIR__ . '/../templates/tasks/create.php';
                $content = ob_get_clean();
                include __DIR__ . '/../templates/layout/header.php';
                echo $content;
                include __DIR__ . '/../templates/layout/footer.php';
                break;
            }
            $result = $task->create(
                $_SESSION['user_id'],
                $_POST['title'] ?? '',
                $_POST['description'] ?? '',
                $_POST['due_date'] ?? null
            );
            if ($result['success']) {
                header('Location: /tasks?created=1');
                exit;
            }
            $error = 'Error al crear la tarea.';
            $title = 'Nueva Tarea';
            ob_start();
            include __DIR__ . '/../templates/tasks/create.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;
        case preg_match('#^/tasks/(\d+)$#', $uri, $m) === 1 && $method === 'GET':
            Auth::requireLogin();
            $taskData = $task->getById((int)$m[1]);
            if (!$taskData || $taskData['user_id'] != $_SESSION['user_id']) {
                http_response_code(404);
                echo 'Tarea no encontrada.';
                exit;
            }
            $task = $taskData;
            $title = 'Ver Tarea';
            ob_start();
            include __DIR__ . '/../templates/tasks/show.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case preg_match('#^/tasks/(\d+)/edit$#', $uri, $m) === 1 && $method === 'GET':
            Auth::requireLogin();
            $taskData = $task->getById((int)$m[1]);
            if (!$taskData || $taskData['user_id'] != $_SESSION['user_id']) {
                http_response_code(404);
                echo 'Tarea no encontrada.';
                exit;
            }
            $task = $taskData;
            $title = 'Editar Tarea';
            ob_start();
            include __DIR__ . '/../templates/tasks/edit.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case preg_match('#^/tasks/(\d+)/edit$#', $uri, $m) === 1 && $method === 'POST':
            Auth::requireLogin();
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Error de validación CSRF.';
                $taskData = $task->getById((int)$m[1]);
                $task = $taskData;
                $title = 'Editar Tarea';
                ob_start();
                include __DIR__ . '/../templates/tasks/edit.php';
                $content = ob_get_clean();
                include __DIR__ . '/../templates/layout/header.php';
                echo $content;
                include __DIR__ . '/../templates/layout/footer.php';
                break;
            }
            $result = $task->update((int)$m[1], [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'pending',
                'due_date' => $_POST['due_date'] ?? null,
            ]);
            if ($result['success']) {
                header('Location: /tasks?updated=1');
                exit;
            }
            $error = 'Error al actualizar la tarea.';
            $taskData = $task->getById((int)$m[1]);
            $task = $taskData;
            $title = 'Editar Tarea';
            ob_start();
            include __DIR__ . '/../templates/tasks/edit.php';
            $content = ob_get_clean();
            include __DIR__ . '/../templates/layout/header.php';
            echo $content;
            include __DIR__ . '/../templates/layout/footer.php';
            break;

        case preg_match('#^/tasks/(\d+)/delete$#', $uri, $m) === 1 && $method === 'POST':
            Auth::requireLogin();
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                http_response_code(403);
                die('Error CSRF');
            }
            $task->delete((int)$m[1]);
            header('Location: /tasks?deleted=1');
            exit;

        default:
            http_response_code(404);
            $title = '404 - No Encontrado';
            include __DIR__ . '/../templates/layout/header.php';
            echo '<div class="alert alert-warning"><h2>Página no encontrada</h2><p>La ruta solicitada no existe.</p></div>';
            include __DIR__ . '/../templates/layout/footer.php';
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    $title = 'Error del Servidor';
    include __DIR__ . '/../templates/layout/header.php';
    echo '<div class="alert alert-danger"><h2>Error Interno</h2><p>' . Security::escape($e->getMessage()) . '</p></div>';
    include __DIR__ . '/../templates/layout/footer.php';
}
