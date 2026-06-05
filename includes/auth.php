<?php
// includes/auth.php

function cekLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit();
    }
}

function cekAdmin() {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../user/dashboard.php");
        exit();
    }
}

function cekSudahLogin() {
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit();
    }
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
