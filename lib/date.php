<?php

$fecha = getdate();
$dia_date = date("d");
$anyo = date("Y");
$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
$dia = $dias[$fecha["wday"]];
$mes = $meses[$fecha["mon"] - 1];