<?php

if ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1') {
  // Entorno local
  define('DB_SERVER', 'localhost');
  define('DB_NAME', 'deco_hogar');
  define('DB_USER', 'root');
  define('DB_PASS', 'root');
} else {
  // Entorno remoto
  define('DB_SERVER', 'localhost');
  define('DB_NAME', 'id21665487_deco_hogar_db');
  define('DB_USER', 'id21665487_administrator');
  define('DB_PASS', 'w@GrHJK!!79w');
}