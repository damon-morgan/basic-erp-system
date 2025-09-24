<?php
session_start();
session_unset();   // remove all session variables
session_destroy(); // destroy the session itself

// redirect to login page
header("Location: login.php");
exit();