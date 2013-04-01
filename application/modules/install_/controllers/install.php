<?php
class Install extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->template['admin'] = true;
		$this->template['module'] = 'install';
		$this->config->load('config');
		$this->load->model('install_model', 'model');
		$this->load->library('form_validation');
		$this->file_install = 'install.sql';
	}

	public function index()
	{
		redirect($this->template['module'].'/step1');
	}

	public function step1 ()
	{
		if($this->session->userdata('confirm_success') == 1) redirect($this->template['module'].'/success');

		$this->template['title'] = 'Etape 1';

		//On recupère les informations de connexion
		$fields_validation = array(
			array(
				'field'   => 	'database_hostname',
				'label'   => 	'Serveur Sql',
				'rules'   => 	'trim|required'
			),
			array(
				'field'   => 	'database_name',
				'label'   => 	'Base de données',
				'rules'   => 	'trim|required'
			),
			array(
				'field'   => 	'database_username',
				'label'   => 	'Nom d\'utilisateur',
				'rules'   => 	'trim|required'
			),
			array(
				'field'   => 	'database_password',
				'label'   => 	'Mot de passe',
				'rules'   => 	'trim|required|matches[database_password_confirm]'
			),
			array(
				'field'   => 	'database_password_confirm',
				'label'   => 	'Mot de passe confirmation',
				'rules'   => 	'trim|required'
			)
		);

		$this->form_validation->set_rules($fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, 'install', 'step1');
		}
		else
		{
			if(!$this->model->test_connexion($this->input->post('database_hostname'), $this->input->post('database_username'), $this->input->post('database_password')))
			{
				$this->template['alerte'] = 'Impossible de se connecter au serveur Mysql avec les informations communiquées';
				$this->css->add(array('admin'));
				$this->javascripts->add(array('jquery', 'sitelib'));
				$this->layout->load($this->template, 'install', 'step1');
			}
			else
			{
				$this->session->set_userdata('database_username', $this->input->post('database_username'));
				$this->session->set_userdata('database_password', $this->input->post('database_password'));
				$this->session->set_userdata('database_hostname', $this->input->post('database_hostname'));
				$this->session->set_userdata('database_name', $this->input->post('database_name'));
				redirect($this->template['module'].'/step2');
			}
		}

	}

	public function step2 ()
	{
		if(!$this->session->userdata('database_username') && !$this->session->userdata('database_password') && !$this->session->userdata('database_hostname') && !$this->session->userdata('database_name')) redirect($this->template['module'].'/step1');
		if($this->session->userdata('confirm_success') == 1) redirect($this->template['module'].'/success');

		$this->template['title'] = 'Etape 2';

		//On recupère les informations de admin
		$fields_validation = array(
			array(
				'field'   => 	'admin_email',
				'label'   => 	'Email',
				'rules'   => 	'trim|required|valid_email'
			),
			array(
				'field'   => 	'admin_password',
				'label'   => 	'Mot de passe',
				'rules'   => 	'trim|required|matches[admin_password_confirm]'
			),
			array(
				'field'   => 	'admin_password_confirm',
				'label'   => 	'Mot de passe confirmation',
				'rules'   => 	'trim|required'
			)
		);

		$this->form_validation->set_rules($fields_validation);

		$this->form_validation->set_error_delimiters('', '<br />');

		if ($this->form_validation->run() == FALSE)
		{
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, 'install', 'step2');
		}
		else
		{
			$this->session->set_userdata('admin_email', $this->input->post('admin_email'));
			$this->session->set_userdata('admin_password', $this->input->post('admin_password'));
			redirect($this->template['module'].'/step3');
		}

	}

	public function step3 ()
	{
		if(!$this->session->userdata('admin_email') && !$this->session->userdata('admin_password')) redirect($this->template['module'].'/step2');
		if($this->session->userdata('confirm_success') == 1) redirect($this->template['module'].'/success');

		$this->template['title'] = 'Etape 3';

		//On vérifie les droits des fichiers et dossiers
		if ($this->input->post('confirm') != 1)
		{
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, 'install', 'step3');
		}
		else
		{
			$this->session->set_userdata('confirm_rights', 1);
			redirect($this->template['module'].'/step4');
		}

	}

	public function step4 ()
	{
		if($this->session->userdata('confirm_rights') != 1) redirect($this->template['module'].'/step3');
		if($this->session->userdata('confirm_success') == 1) redirect($this->template['module'].'/success');

		$this->template['title'] = 'Etape 4';

		//On crée les tables
		if (!$this->input->post('submit'))
		{
			$this->css->add(array('admin'));
			$this->javascripts->add(array('jquery', 'sitelib'));
			$this->layout->load($this->template, 'install', 'step4');
		}
		else
		{
			$this->load->helper('file');

			//Enregistrement du fichier database.php
			$this->model->write_database($this->session->userdata('database_hostname'), $this->session->userdata('database_username'), $this->session->userdata('database_password'),$this->session->userdata('database_name'));

			//Création des tables
			$this->model->import_tables($this->session->userdata('database_hostname'), $this->session->userdata('database_username'), $this->session->userdata('database_password'),$this->session->userdata('database_name'));

			//Enregistrement du compte admin en base
			$this->model->update_admin($this->session->userdata('admin_email'), $this->session->userdata('admin_password'));

			//Update settings
			$this->model->update_settings();

			$this->system->clear_cache();

			$this->session->set_userdata('confirm_success', 1);
			redirect($this->template['module'].'/success');
		}

	}

	public function success ()
	{
		if($this->session->userdata('confirm_success') != 1) redirect($this->template['module'].'/step4');

		$this->session->unset_userdata('database_username');
		$this->session->unset_userdata('database_password');
		$this->session->unset_userdata('database_hostname');
		$this->session->unset_userdata('database_name');
		$this->session->unset_userdata('root_email');
		$this->session->unset_userdata('root_password');
		$this->session->unset_userdata('confirm_rights');

		if(is_file('./'.$this->config->item('medias_folder').'/tmp/'.$this->file_install)) unlink('./'.$this->config->item('medias_folder').'/tmp/'.$this->file_install);

		$this->template['title'] = 'Succès';

		$this->css->add(array('admin'));
		$this->javascripts->add(array('jquery', 'sitelib'));
		$this->layout->load($this->template, 'install', 'success');

	}

}