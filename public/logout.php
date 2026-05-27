<?php
require_once dirname(__DIR__) . '/src/config/autoload.php';

use App\Middleware\Auth;

Auth::logout();
header('Location: /login.php');
exit;
