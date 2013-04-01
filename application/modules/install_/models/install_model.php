<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class install_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_connexion($database_hostname, $database_username, $database_password)
	{
		$connexion = @mysql_connect($database_hostname, $database_username, $database_password);
		if (!$connexion) {
			return false;
		} else {
			return true;
		}
	}

	public function write_database($database_hostname = '', $database_username = '', $database_password = '', $database_name = '')
	{
		$data = '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the \'Database Connection\'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	[\'hostname\'] The hostname of your database server.
|	[\'username\'] The username used to connect to the database
|	[\'password\'] The password used to connect to the database
|	[\'database\'] The name of the database you want to connect to
|	[\'dbdriver\'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	[\'dbprefix\'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	[\'pconnect\'] TRUE/FALSE - Whether to use a persistent connection
|	[\'db_debug\'] TRUE/FALSE - Whether database errors should be displayed.
|	[\'cache_on\'] TRUE/FALSE - Enables/disables query caching
|	[\'cachedir\'] The path to the folder where cache files should be stored
|	[\'char_set\'] The character set used in communicating with the database
|	[\'dbcollat\'] The character collation used in communicating with the database
|	[\'swap_pre\'] A default table prefix that should be swapped with the dbprefix
|	[\'autoinit\'] Whether or not to automatically initialize the database.
|	[\'stricton\'] TRUE/FALSE - forces \'Strict Mode\' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the \'default\' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = \'default\';
$active_record = TRUE;

if($_SERVER[\'SERVER_NAME\'] == \'localhost\' || $_SERVER[\'SERVER_NAME\'] == \'127.0.0.1\')
{
	$db[\'default\'][\'hostname\'] = \''.$database_hostname.'\';
	$db[\'default\'][\'username\'] = \''.$database_username.'\';
	$db[\'default\'][\'password\'] = \''.$database_password.'\';
	$db[\'default\'][\'database\'] = \''.$database_name.'\';
}
else {
	$db[\'default\'][\'hostname\'] = \''.$database_hostname.'\';
	$db[\'default\'][\'username\'] = \''.$database_username.'\';
	$db[\'default\'][\'password\'] = \''.$database_password.'\';
	$db[\'default\'][\'database\'] = \''.$database_name.'\';
}

$db[\'default\'][\'dbdriver\'] = \'mysql\';
$db[\'default\'][\'dbprefix\'] = \'ci_\';
$db[\'default\'][\'pconnect\'] = TRUE;
$db[\'default\'][\'db_debug\'] = TRUE;
$db[\'default\'][\'cache_on\'] = FALSE;
$db[\'default\'][\'cachedir\'] = \'\';
$db[\'default\'][\'char_set\'] = \'utf8\';
$db[\'default\'][\'dbcollat\'] = \'utf8_general_ci\';
$db[\'default\'][\'swap_pre\'] = \'\';
$db[\'default\'][\'autoinit\'] = TRUE;
$db[\'default\'][\'stricton\'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */
';

		if (!write_file('./'.APPPATH.'/config/database.php', $data))
		{
			 $return['notification'] =  'Fichier database.php enregistré';
		}
		else
		{
			 $return['alerte'] = 'Impossible d\'écrire dans le fichier database.php';
		}

		@chmod('./'.APPPATH.'/config/database.php', 0644);

		return $return;
	}

	public function import_tables($database_hostname = '', $database_username = '', $database_password = '', $database_name = '')
	{
		$errmsg = "Tables importées avec succès";
		if(is_file('./'.$this->config->item('backup_folder').'/'.$this->file_install) && is_dir('./'.$this->config->item('medias_folder').'/tmp'))
		{
			if(@copy('./'.$this->config->item('backup_folder').'/'.$this->file_install, './'.$this->config->item('medias_folder').'/tmp/'.$this->file_install))
			{
				if(is_file('./'.$this->config->item('medias_folder').'/tmp/'.$this->file_install))
				{
					$lines = file('./'.$this->config->item('medias_folder').'/tmp/'.$this->file_install);

					// Lecture du fichier
					if(!$lines)
					{
						$errmsg .= "Impossible d'ouvrir le fichier ".$lines."<br />";
					}

					/* Get rid of the comments and form one jumbo line */
					$scriptfile = false;
					if($lines)
					{
						foreach($lines as $line)
						{
							$line = trim($line);
							if(!@ereg('^--', $line)) {
								$scriptfile.=" ".$line;
							}
						}
					}

					if(!$scriptfile) {
						$errmsg .= "Fichier vide ".$lines."<br />";
					}

					/* Split the jumbo line into smaller lines */
					$queries = explode(';', $scriptfile);

					/* Run each line as a query */
					$this->load->database();
					$this->_delete_tables($database_name);

					foreach($queries as $query)
					{
						$query = trim($query);
						if($query == "") {continue;}
						if(!$this->db->query($query.';'))
						{
							$errmsg .= "Requête ".$query." impossible à exécuter<br />";
						}
					}

				}
			}
		}
		return $errmsg;

	}

	public function update_admin($admin_email = '', $admin_password = '')
	{
		$admin_password = $this->_prep_password($admin_password);
		$data = array(
			'password' 		=> $admin_password,
			'email' 		=> $admin_email,
			'registered'	=> mktime(),
			'online'		=> 1
		);
		$this->db->where(array('id' => 2, 'username' => 'admin'))->update($this->config->item('table_users'), $data);
	}

	public function update_settings()
	{
		$pages_settings = array(
			'value' => serialize(array('page_home' => 'index', 'page_publish_feed' => 1, 'per_page' => 10))
		);
		$this->db->where(array('name' => 'pages_settings'))->update($this->config->item('table_settings'), $pages_settings);

	}

	private function _delete_tables($database_name = '')
	{
		$query = $this->db->query("SHOW TABLES");
		foreach ($query->result_array() as $row)
		{
			$table_name = $row['Tables_in_'.$database_name];
			$this->db->query("DROP TABLE ".$table_name);
		}
	}

	private function _prep_password($password = '')
	{
		return $this->encrypt->sha1($password.$this->config->item('encryption_key'));
	}

}