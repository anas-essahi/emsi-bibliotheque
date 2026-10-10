<?php
require_once 'includes/config.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: catalogue.php");
    }
} else {
    header("Location: login.php");
}
exit();