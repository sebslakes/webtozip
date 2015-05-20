<?php
/*
 * Script que debe ser ejecutado por crontab o cronjobs
 * Este script elimina los archivos descargados que tengan una antiguedad superior a 10 minutos
 * @author SebsLakes twitter.com/sebslakes
 */
 
 include('webtozip.php');
 $web = new Webtozip();
 $web->recycler();
?>