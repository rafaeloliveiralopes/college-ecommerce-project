<?php

date_default_timezone_set('America/Sao_Paulo');

if (!function_exists('load_project_env')) {
  function load_project_env($path)
  {
    static $loaded_paths = array();

    if (isset($loaded_paths[$path]) || !is_file($path) || !is_readable($path)) {
      return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
      return;
    }

    foreach ($lines as $line) {
      $line = trim($line);

      if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
        continue;
      }

      list($key, $value) = explode('=', $line, 2);
      $key = trim($key);
      $value = trim($value);

      if ($key === '') {
        continue;
      }

      if (
        (substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
      ) {
        $value = substr($value, 1, -1);
      }

      if (!array_key_exists($key, $_ENV)) {
        $_ENV[$key] = $value;
      }

      if (!array_key_exists($key, $_SERVER)) {
        $_SERVER[$key] = $value;
      }

      putenv($key . '=' . $value);
    }

    $loaded_paths[$path] = true;
  }
}

if (!function_exists('project_env')) {
  function project_env($key, $default = '')
  {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
      return $default;
    }

    return $value;
  }
}

load_project_env(dirname(__DIR__) . '/.env');

$db_host = project_env('DB_HOST', 'localhost');
$db_user = project_env('DB_USER', 'root');
$db_password = project_env('DB_PASSWORD', '');
$db_name = project_env('DB_NAME', 'project_db');

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name)
  or die('Nao foi possivel conectar ao banco de dados.');

?>
