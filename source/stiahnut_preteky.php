<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');

if (!is_admin()) {
    echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
    die();
}