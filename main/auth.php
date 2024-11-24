<?php
function checkAccess($requiredRole)
{
    /*if (!isset($_SESSION['logged_in'])) {
        // User not logged in
        
        header('Location: ../index.php');
         exit();
    } */

    $staff_logged_in = isset($_SESSION["staff"]["role"]) && $_SESSION["staff"]["role"] == $requiredRole;
    $admin_logged_in = isset($_SESSION["admin"]["role"]) && $_SESSION["admin"]["role"] == $requiredRole;


    if (($requiredRole == "staff" && !$staff_logged_in) || ($requiredRole == "admin" && !$admin_logged_in)) {
        header('Location: ../index.php');
        exit();
    }

}
