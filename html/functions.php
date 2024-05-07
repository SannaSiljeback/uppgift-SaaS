<?php

    session_start();

    function user_has_role($role) {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role) {
            return true;
        } else {
            return false;
        }
    }

    function is_signed_in() {
        if (isset($_SESSION['is_signed_in']) && $_SESSION['is_signed_in'])
        {
            return true;
        } else {
            return false;
        }  
    }

?>