<?php
require_once __DIR__ . "/../../middleware/auth.php";

sessionStart();
session_destroy();

header('location: ../../pages/home/index.php');
exit;
?>
