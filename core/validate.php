<?php  
/**
 * Shared validation utilities for user input.
 */

function validatePassword($password) {

	// Enforce a minimum password policy with lowercase, uppercase, and digits
	if (strlen($password) >= 8) {
		$hasLower = false;
		$hasUpper = false;
		$hasNumber = false;

	    for ($i = 0; $i < strlen($password); $i++) {

	    	if (ctype_lower($password[$i])) {
	    		$hasLower = true; 
	        } 

	        elseif (ctype_upper($password[$i])) {
	            $hasUpper = true; 
	        } 

	        elseif (ctype_digit($password[$i])) {
	            $hasNumber = true;
	        }

	        if ($hasLower && $hasUpper && $hasNumber) {
	            return true; 
	        }
	    }
	}

	else {
		return false; 
	}
}

function sanitizeInput($data) {

  // Normalize and escape user-submitted values before saving or displaying
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;

}

?>