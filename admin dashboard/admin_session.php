<?php
 if (session_status() == PHP_SESSION_NONE) {
    session_name('adminsession');
    session_start(); 
}

?>