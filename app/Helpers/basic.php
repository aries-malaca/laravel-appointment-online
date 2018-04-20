<?php
    if (! function_exists('delegation')) {
        function delegation($user){
            return $user['gender']=='male' ? 'Mr.':'Ms.';
        }
    }
?>