<?php

/**
 * get_token_information
 * 
 * @package Allergic
 */
 
function get_token_information () {
    
    global $container;
    global $secret_token;
    global $token_validaty_time;
    
    
    // Get the token
    $_token = $container['api_token_credentials'];
    
    
    // Token has to be provided
    if ( ! $_token) {
        return array ();
    }
    
    
    // Decode information about the token
    try {
        $decoded = JWT::decode ($_token, $secret_token);
        
    } catch (Exception $e) {
        return false;
        
    }
    
    return (array) $decoded;
    
    
}

/**
 * is_token_valid
 *
 * This function secures some API calls
 *
 * @param $token String
 *
 * @return Response|null
 *
 * @package Allergic
 */
function is_token_valid ($token = null) {
    
    global $token_validaty_time;
    
    
    // Get the token
    if (! $token) {
        $token = get_token_information ();
    }
    
    
    
    // Token has to be provided
    if ( ! $token) {
        return new JSONResponse (['ok' => false, 'message' => 'Token credentials not provided'], 401);
    }

    
    // Fetch the results
    $time = $token['time'];
    $user_id = $token['user_id'];
    
    
    // Get the user
    $user = new User ($user_id);
    
    
    // Check validity of the account
    if ( ! $user->isAccountValidated ()) {
        return new JSONResponse (['ok' => false, 'message' => 'User account has been disabled'], 401);
    }
    
    
    // Check token time
    if ((time () - $time) > $token_validaty_time) {
        return new JSONResponse (['ok' => false, 'message' => 'Token has been expired'], 401);
    }

}


/**
 * validate_api_auth_token
 *
 * This function secures some API calls
 *
 * @package Allergic
 */
function validate_api_auth_token () {
    
    $response = is_token_valid ();
    if ($response) {
        die ($response);
    }
}



/**
 * Pluck an array of values from an array. (Only for PHP 5.3+)
 *
 * @param  $array - data
 * @param  $key - value you want to pluck from array
 *
 * @return plucked array only with key data
 */
function pluck ($array, $key) {
    return array_map (function ($v) use ($key) {
        return is_object ($v) ? $v->$key : $v[$key];
    }, $array);
}


/**
 * random_float
 *
 * Returns a random number between a range
 *
 * @param $min float
 * @param $max float
 *
 * @package Allergic
 */
function random_float ($min, $max) {
   return ($min + lcg_value() * (abs ($max-$min)));
}


/**
 * generate_password
 *
 * @url https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
 *
 * @param $length int
 *
 * @return String
 */
function generate_password ($length = 8) {

    // Source
    $source = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    
    
    // Return shuffle string
    return substr (str_shuffle ($source), 0, $length);

}


/**
 * dd
 *
 * Debug and die
 *
 * @param $length int
 *
 */
function dd ($var) {
    echo '<pre>';
    print_r ($var);
    echo '</pre>';
    die ();
}
