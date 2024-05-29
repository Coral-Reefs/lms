<?php
// to access session variables and methods
session_start();

// delete all session variables
session_unset();

// delete all data registered in the session
session_destroy();

header("Location: /");