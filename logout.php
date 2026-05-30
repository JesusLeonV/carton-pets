<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: login.php?status=logout_exitoso");
exit();
?>