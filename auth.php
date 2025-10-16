<?php
session_start();

function authenticate($username, $password) {
    $credentials = json_decode(file_get_contents('admin_credentials.json'), true);

    if ($username === $credentials['username'] && $password === $credentials['password']) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function logout() {
    session_destroy();
}
?>