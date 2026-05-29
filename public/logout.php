<?php
require_once __DIR__ . '/../src/config/autoload.php';

use App\middleware\Auth;

Auth::logout();
header('Location: /login.php');
exit;
