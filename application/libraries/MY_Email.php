<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Email extends CI_Email
{
	function MY_Email()
	{
		$this->obj =& get_instance();
	}

	public function send_mail($from = '', $from_name = '', $to = '', $to_name = '', $subject = '', $message_alt = '', $message = '', $is_html = false, $pj = '', $footer = TRUE)
	{
		require './'.APPPATH.'libraries/phpmailer/PHPMailer.php';
		$mail = new PHPMailer(true);

		if($this->obj->system->smtp_is == 1)
		{
			$mail->IsSMTP();
			$mail->Host       = $this->obj->system->smtp_host;
			$mail->SMTPDebug  = 0;
			$mail->SMTPAuth   = true;
			$mail->Port       = $this->obj->system->smtp_port;
			$mail->Username   = $this->obj->system->smtp_username;
			$mail->Password   = $this->obj->system->smtp_password;
		}
		$mail->IsHTML($is_html);
		$mail->CharSet = "UTF-8";

		if($footer) $message .= $this->footer($is_html);

		$mail->AddAddress($to, $to_name);
		$mail->SetFrom($from, $from_name);
		$mail->Subject = $subject;
		$mail->Body = $message;

		if(is_array($pj))
		{
			foreach($pj as $file)
			{
				if(is_file($file)) $mail->AddAttachment($file);
			}
		}

		if($send = $mail->Send())
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public function footer($is_html = false)
	{
		$separator = "\n";
		if($is_html) $separator = '<br />';
		$message = $separator.$separator.'----------------------------------------'.$separator;
		$message .= $this->obj->lang->line('mail_footer_cordialy').$separator;
		$message .= $this->obj->system->site_name.$separator;
		$message .= $this->obj->system->site_adress.$separator;
		if($this->obj->system->site_adress_next) $message .= $this->obj->system->site_adress_next.$separator;
		$message .=	$this->obj->system->site_post_code.' '.$this->obj->system->site_city.$separator;
		$message .=	$this->obj->lang->line('mail_footer_phone').' '.$this->obj->system->site_phone.$separator;
		$message .=	site_url();

		return $message;

	}
}