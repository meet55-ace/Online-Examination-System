<?php
 if (session_status() == PHP_SESSION_NONE) {
    session_name('facultysession');
    session_start(); 
}

?>