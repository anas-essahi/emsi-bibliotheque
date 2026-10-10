<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../classes/Database.php';
 if(!isset($_SESSION['user_id'])||$_SESSION['user_type']!=='admin'){
      header('Location: ../../login.php');
       exit();
 }

