<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('div_class')) {
	function div_class($string, $div_class = 'alert alert-danger')
	{
		if($div_class == 'alert alert-error') $div_class = 'alert alert-danger';
		
	    return sprintf('<div class="%s">%s</div>', $div_class, $string);
	}
	
	
}

?>