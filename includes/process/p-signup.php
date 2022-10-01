<?php

    $h = new Helper();
    $msg = '';
    $username = '';

    if (isset($_POST['submit']))
    {        
        $username = $_POST['username'];    
        $farmname = $_POST['farmname'];            
        if ($h->isEmpty(array($username, $_POST['password'], $_POST['farmname'], $_POST['confirm_password'])))
        {
            $msg = 'All fields are required';     
        }
        else if (!$h->isValidLength($username, 6, 100)){
            $msg = 'Username must be between 6 and 100 characters';
        }
        else if (!$h->isValidLength($_POST['password'])){
            $msg = 'Password must be between 8 and 20 characters';
        }
        else if (!$h->isSecure($_POST['password'])){
            $msg = 'Password must contain at least one lowercase character, one uppercase character and one digit';
        }
        else if (!$h->passwordsMatch($_POST['password'], $_POST['confirm_password'])){
            $msg = 'Passwords do not match';
        }        
        else
        {
            $user = new FarmUser($username, $farmname);


            // if ($member->isDuplicateID())
            // {
            //     $msg = "Username already in use";
            // }
        
            $user->insertIntoFarmDB($_POST['password']);

            header("Location: index.php?new=1");                
            }
            
    }