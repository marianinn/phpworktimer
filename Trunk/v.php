<?php
/**
 * Last modified -- 2005-10-20
 * Author: Lazerka Dzmitry <triggeru@yandex.ru>
 *
 * Function v() which echoes variable.
 * Nice replacement for var_dump() and print_r().
 *
 * TODO list:
 *     - Opera + v("mystring") + Ctrl+C = mystring's newlines isn't copied (Opera bug).
 *       May be fixed by removing <pre> in vv().
 *     - Leading and trailing whitespaces must not be cut.
 */

if (!function_exists('v') && !function_exists('vv')) {
/**
 * v()'s assistant.
 * Shows only one line like "(<i>integer</i>) 123\n".
 *
 * @param mixed $value -- value to show info about
 * @return string -- xhtml with one-string info
 */
function vv($value) {

	$result = '';

	if (gettype($value) == 'string') {
		$result .= '(<i>'.gettype($value).'</i>, '.strlen($value).') ';
		$result .= '<b>&lsaquo;</b><span style="font-size:1px"> </span>';
		$result .= '<pre style="display:inline; margin:0; padding:0;">';
		$result .= htmlspecialchars($value);
		$result .= '</pre>';
		$result .= '<span style="font-size:1px"> </span><b>&rsaquo;</b>';
	}
	elseif (gettype($value) == 'array') {
		$result .= '(<i>'.gettype($value).'</i>, '.count($value).')';
	}
	elseif (gettype($value) == 'boolean') {
		$result .= '(<i>'.gettype($value).'</i>) '.($value ? 'TRUE' : 'FALSE');
	}
	elseif (gettype($value) == 'object') {
		$class = get_class($value);
		$fields_qty = count(get_object_vars($value));
		$methods_qty = count(get_class_methods($class));
		$result .= '(<i>'. gettype($value) .'</i>, <b>'. $class .'</b>,';
		$result .= ' <b>'. $fields_qty .'</b><i>f</i>/<b>'. $methods_qty .'</b><i>m</i>)';
	}
	else {
		$result .= '(<i>'.gettype($value).'</i>) '.$value;
	}
	$result .= "<br />\n";


	return $result;
}


/**
 * Shows the whole variable.
 *
 * @param mixed $value -- any param you can imagine
 * @return string -- xhtml which presents your $value full info (returned only if your caller will take it)
 */
function v($value = NULL) {

	$show_object_methods = true;


	$result = '';


	// Getting info about calling place (name, line and filename)
	static $call_cntr = array();// counter of calls from one place
	list($call) = debug_backtrace();
	$file = file($call['file']);

	$matches = array();
	preg_match(
		'/(=?)\\s*v\\s*\\(\\s*(.*?)\\s*\\)\\s*;/',
		$file[$call['line'] - 1],
		$matches
	);

	$should_echo = empty($matches[1]);// echo or return result

	$call['param'] = empty($matches[2]) ? '<i>multiline</i>' : htmlspecialchars($matches[2]);

	if (!isset($call_cntr[$call['file']][$call['line']])) {
		$call_cntr[$call['file']][$call['line']] = 0;
	}
	else {
		$call_cntr[$call['file']][$call['line']]++;
	}


	// Output auxiliary html
	$result .= '<div style="z-index:100; background-color:white; text-align:left;';
	$result .= ' position:relative; top:0; left:0;">';
	$result .= '<fieldset style="font-family:verdana; font-size:14px; color:black;">';
	$result .= '<legend>';


	// Output info about calling place
	$result .= 'Line <b>' . $call['line'] . '</b>';
	$result .= ', &#8470; <b>' . $call_cntr[$call['file']][$call['line']] . '</b>';
	$result .= ', file: ' . $call['file'];
	$result .= ', time: ' . date('H:i:s') . substr(microtime(), 1, 4). '</legend>';


	// Output one-string info about value of param
	if (func_num_args()) {
		$result .= '<b>' . $call['param'] . '</b> => ';
		$result .= vv($value);
	}
	else {
		$value = array();
		$traces = debug_backtrace();
		unset($traces[0]);
		foreach ($traces as $trace) {
			$s = '';
			if(isset($trace['file'])) {
				$s .= $trace['file'] . '(' . $trace['line'] . ') @ ';
			}
			if (!empty($trace['class'])) {
				$s .= $trace['class'];
				$s .= $trace['type'];
			}
			$s .= $trace['function'] . '()';
			$value[] = $s;
		}
	}


	// Walk through arrays and objects recursively
	if (in_array(gettype($value), array('array', 'object'))) {
		// Compose tree to walk through
		$tree = array(); // depth search tree
		$d = 0; //  depth
		$tree[$d] = $value;
		if (gettype($value) == 'object') {
			if ($show_object_methods) {
				$methods = get_class_methods(get_class($value));
				$tree[$d] = array_merge(get_object_vars($value), $methods);
			}
			else {
				$tree[$d] = get_object_vars($value);
			}
		}


		// Walking
		for ($elem = each($tree[$d]); $d >= 0 && $elem; ) {

			list($key, $val) = $elem;

			// Show current node info
			$result .= str_repeat('&nbsp;', ($d + 1)*8);
			$result .= '<b>[' . htmlspecialchars($key) . ']</b> => ';
			$result .= vv($val);


			// Make object like array
			if (gettype($val) == 'object') {
				if ($show_object_methods) {
					$methods = get_class_methods(get_class($val));
					$val = array_merge(get_object_vars($val), $methods);
				}
				else {
					$val = get_object_vars($val);
				}
			}

			// if current node is an array (or was an object), go down along the tree.
			if (gettype($val) == 'array' && count($val) > 0) {
				$d++;
				$tree[$d] = $val;
				$elem = each($tree[$d]);
			}
			else {
				// take next node
				$elem = each($tree[$d]);

				// if no nodes remained, go up until possible
				while (!$elem && $d > 0) {
					$d--;
					$elem = each($tree[$d]);
				}
			}
		}
	}


	$result .= '</fieldset></div>';
	$result .= "\n";


	// Output
	if ($should_echo) {
		echo $result;
	}
	else {
		return $result;
	}
}
}
?>