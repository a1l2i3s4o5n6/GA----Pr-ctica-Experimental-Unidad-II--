<?php

namespace App;

use PDO;

class Auth
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register(string $username, string $email, string $password): array
    {
        if ($this->findByEmail($email)) {
            return ['success' => false, 'error' => 'El email ya está registrado.'];
        }

        if ($this->findByUsername($username)) {
            return ['success' => false, 'error' => 'El nombre de usuario ya existe.'];
        }

        $hashedPassword = Security::hashPassword($password);

        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);

        return ['success' => true];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'error' => 'Credenciales inválidas.'];
        }

        if (!Security::verifyPassword($password, $user['password'])) {
            return ['success' => false, 'error' => 'Credenciales inválidas.'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['logged_in'] = true;

        return ['success' => true];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['logged_in']);
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    private function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    private function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
