<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * PHP Security Project
 *
 * We all know it: user cannot be trusted. If you have not realized this yet -- face the truth. It can have severe consequenses.
 * The purpose of this project is to offer developers powerful libraries to protect themselves and their websites
 * from unwanted data.
 *
 * Reference / Further read:
 * [1] http://phpsec.org/projects/guide/
 * [2] http://ilia.ws/files/phpworks_security.pdf
 * [3] http://www.symantec.com/connect/fr/articles/securing-php-step-step
 * [4] http://www.phparch.com/2010/07/08/never-use-_get-again/ (We should probably adapt this method)
 *
 * @author	David Steinsland
 * @url		http://php.davidsteinsland.net/php-security-wrapper/
 * @license	Creative Commons
 */

/**
 * Rules for sanitzing output.
 * All these methods have one thing in common: it manipulates the data
 */
class Sanitation
{
	public function __construct () { }

	/**
	 * Cleans the string from <, > and /
	 * Useful when outputting data from database
	 * @param string $data
	 * @return string
	 */
	public function xss_clean ($data)
	{
		return htmlspecialchars (stripslashes ($data));
	}

	/**
	 * Casts a data type to integer
	 * @param mixed $data
	 * @return integer
	 */
	public function integer ($data)
	{
		return (int) $data;
	}

	/**
	 * Casts a data type to string
	 * @param mixed $data
	 * @return string
	 */
	public function string ($data)
	{
		return (string) $data;
	}

	/**
	 * Removes all non-alpha-numerical characters
	 * @param mixed $data
	 * @return string
	 */
	public function alnum ($data)
	{
		return preg_replace ('/[^\w]/', '', $data);
	}

	/**
	 * Removes all non-alphabetical characters
	 * @param mixed $data
	 * @return string
	 */
	public function alpha ($data)
	{
		return preg_replace ('/[^\\pL]/', '', $data);
	}

	/**
	 * Strips any adjacent characters to one
	 * @param mixed $data
	 * @param mixed $remove
	 * @return string
	 */
	public function remove_multiple ($data, $remove)
	{
		return preg_replace ('/[' . $remove . ']{2,}/', $remove, $data);
	}


	/**
	 * Filters the data with specified filter method
	 * Compatible with filter_var
	 * @param mixed $data
	 * @param integer $filter
	 * @return mixed
	 */
	public function filter ($data, $filter = FILTER_DEFAULT)
	{
		$available_filters = array (
			FILTER_SANITIZE_EMAIL,
			FILTER_SANITIZE_ENCODED,
			FILTER_SANITIZE_MAGIC_QUOTES,
			FILTER_SANITIZE_NUMBER_FLOAT,
			FILTER_SANITIZE_NUMBER_INT,
			FILTER_SANITIZE_SPECIAL_CHARS,
			FILTER_SANITIZE_STRING,
			FILTER_SANITIZE_STRIPPED,
			FILTER_SANITIZE_URL,
			FILTER_UNSAFE_RAW,
			FILTER_DEFAULT
		);

		if ( ! in_array ($filter, $available_filters)) {
			trigger_error ('Selected filter does not exist.', E_USER_ERROR);
		}

		return filter_var ($data, $filter);
	}

	// Maybe remove function to encourage Prepared statements instead?
	public function quote_smart ($data)
	{
		// quote_smart function here.
	}
}