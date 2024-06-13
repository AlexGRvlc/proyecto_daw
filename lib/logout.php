<?php

session_start();
session_unset();
session_destroy();

$caduca = time() - 95365;

if (isset($_COOKIE['nombre'])) {
    setcookie('id', $_SESSION['id_socio'], $caduca, "/");
    setcookie('nombre', $_SESSION['nombre'], $caduca, "/");
    setcookie('img', $_SESSION['imagen'], $caduca, "/");
}

header("Location: ../index.html");

?>