<?php

if ($_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '::1') {
  // Entorno local
  define('APP_URL', "http://localhost/deco_hogar/public_html/");
} else {
  // Entorno remoto en 000webhost
  define('APP_URL', "https://deco-hogar-app.000webhostapp.com/");
}

const APP_NAME = "Deco Hogar";
const APP_SESION_NAME = "APP";

date_default_timezone_set("America/Lima");

const MONEY_SYMBOL = "S/.";
