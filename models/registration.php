<?php

class registration {

	private $data = array('username'=>'','password_hash'=>'','confirm_password'=>'','email'=>'', 'name'=>'');
	private $check = array('values-exists'=>true, 'username-exists'=>false, 'email-exists'=>false, 'password-match'=>true, 'email-validation' => true, 'password-length'=>true, 'dob'=>true);
	private $acceptedCheck = array('values-exists'=>true, 'username-exists'=>false, 'email-exists'=>false, 'password-match'=>true, 'email-validation'=>true, 'password-length'=>true, 'dob'=>true);
	private $allgood = false;
	private $error = '';
	private $registry;
	private $socialLogin = false;
	
	public function __construct(Registry $registry) {
		$this->registry = $registry;
		if(isset($_POST['signup_submit'])) {
			foreach($this->data as $fieldRequired=>$fieldValue) {
				if(!isset($_POST[$fieldRequired]) || $_POST[$fieldRequired]=='') {
					$this->check['values-exists'] = false;
					break;
				}
				$this->data[$fieldRequired] = $this->registry->getObject('db')->sanitizeData($_POST[$fieldRequired]);
			}
			try {
				$this->data['name'] = $this->registry->getObject('db')->sanitizeData($_POST['name']);
				$this->data['gender'] = $this->registry->getObject('db')->sanitizeData($_POST['gender']);
				$this->data['dob'] = $this->registry->getObject('db')->sanitizeData($_POST['dob']);
				if($this->check['values-exists']) {
					$this->checkSubmitedValues();
					if($this->allgood) {
						$this->preocessRegistration();
						$this->registry->getObject('auth')->checkForAuthentication();
						if($this->registry->getObject('auth')->isloggedIn()) {
							if(!isset($_SESSION['urlback'])) {
								header("location:{$this->registry->getSetting('siteurl')}home");
							} else {
								$url = $_SESSION['urlback'];
								unset($_SESSION['urlback']);
								header("location:{$url}");
							}
							exit;
						}
					}
					else {
						$this->registry->getObject('template')->addTemplateBit('error','error.html');
						$this->registry->getObject('template')->getPage()->addTag('errorlog',$this->error);
					}
				}
				else {
					$error = 'Please fill all the marked fields.';
					$this->registry->getObject('template')->addTemplateBit('error','error.html');
					$this->registry->getObject('template')->getPage()->addTag('errorlog',$error);
				}
			} catch(storeException $e) {
				$this->registry->log->logError($e->completeException());
				$this->registry->getObject('template')->addTemplateBit('error','error.html');
				$this->registry->getObject('template')->getPage()->addTag('errorlog','Sorry we encountered some problem while registering you, Please try again later.');
			}
		}
		elseif($this->registry->getObject('url')->getURLBit(2)=='facebook') {
			$this->socialLogin = true;
			require_once(ROOT_DIRECTORY.'models/facebookRegister.php');
			try {
				$fb = new facebookRegisterController($this->registry);
				if($fb->facebookLogin()) {
					$this->data = $fb->getData();
					$this->preocessRegistration();
					$this->registry->getObject('auth')->checkForAuthentication();
					if($this->registry->getObject('auth')->isloggedIn()) {
						$url = $this->registry->getSetting('siteurl');
						if(!isset($_SESSION['urlback'])) {
							header("location:{$this->registry->getSetting('siteurl')}home");
						} else {
							$url = $_SESSION['urlback'];
							unset($_SESSION['urlback']);
							header("location:{$url}");
						}
					}
				}
			} catch(storeException $e) {
				$this->registry->log->logError($e->completeException());
				$this->registry->getObject('template')->addTemplateBit('error','error.html');
				$this->registry->getObject('template')->getPage()->addTag('errorlog','Sorry we encountered some problem with facebook login, Please try again later.');
			}
		}
	}
	
	/*
	 * Check submited Value
	 */
	private function checkSubmitedValues() {
		//username Check
		$query = "SELECT * FROM users WHERE username = '{$this->data['username']}'";
		$this->registry->getObject('db')->executeQuery($query);
		if($this->registry->getObject('db')->numRows()>=1) {
			$this->check['username-exists'] = true;
			$this->error .= 'Username Exists.<br>';
		}

		//email exist check
		$query = "SELECT * FROM users WHERE email = '{$this->data['email']}'";
		$this->registry->getObject('db')->executeQuery($query);
		if($this->registry->getObject('db')->numRows()>=1) {
			$this->check['email-exists'] = true;
			$this->error .= 'Email Exists.<br>';
		}
		
		//email validation
		// email headers
		if(strpos((urldecode($this->data['email'])),"\r" )===true||strpos((urldecode($this->data['email'])),"\n")===true) {
			$this->check['email-validation'] = false;
			$this->error .= 'Wrong Email.<br>';
		}
		
		// email valid
		if( ! preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})^", $this->data['email'])) {
			$this->check['email-validation'] = false;
			$this->error .= 'Wrong Email.<br>';

		}
		
		//password check
		if($this->data['password_hash']!=$this->data['confirm_password']) {
			$this->check['password-match'] = false;
			$this->error .= 'Password Doesn\'t Match.<br>';
		}
		else {
			unset($this->data['confirm_password']);
		}
		
		if(strlen($this->data['password_hash'])<6) {
			$this->check['password-length'] = false;
			$this->error .= 'Password must be more than 6 characters.<br>';
		}
		
		//dob check
		if($this->data['dob']!='') {
			$pattern = '#^((0[1-9])|([1-2][0-9])|(3[0-1]))/((0[1-9])|(1[0-2]))/([1-9][1-9][1-9][1-9])$#';
			if (1 !== preg_match($pattern, $this->data['dob'])) {
	   			$this->check['dob'] = false;
	   			$this->error .= $this->data['dob'].' Date Of birth is not Valid. (Check Format of Date (dd/mm/yyyy)).<br>';
			}
		}
		
		if($this->check==$this->acceptedCheck) {
			$this->allgood = true;
		}
	}
	
	private function preocessRegistration() {
		try {
			$this->registry->createAndStoreObject('passwordHash','hash');
			$size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
			$saltString = mcrypt_create_iv($size, MCRYPT_DEV_RANDOM);
			$salt = $this->registry->getObject('hash')->create_hash($saltString);
			$this->data['password_salt'] = $salt;
			$this->data['password_hash'] = sha1(md5($this->data['password_hash']));
			$this->data['password_hash'] = crypt($this->data['password_hash'], $salt);
			$this->data['password_hash'] = $this->registry->getObject('hash')->create_hash($this->data['password_hash']);
			$this->data['active'] = 1;
			$this->data['admin'] = 0;
			$this->data['banned'] = 0;
			$this->data['deleted'] = 0;
			$this->data['confirmed'] = 0;
			
			$this->data['confirm_code'] = '';
			if($this->socialLogin) {
				$this->data['confirmed'] = 1;
			}
			$session_user_uid = crypt(sha1(md5($this->data['username'])), $salt);
			if(!$this->socialLogin) {
				//$this->sendConfirmationCode();
			}
			$this->data['session_user_uid'] = $this->registry->getObject('hash')->create_hash($session_user_uid);
			$this->registry->getObject('db')->insertRecords('users',$this->data);
			$_SESSION['session_user_uid'] = $this->data['session_user_uid'];
		} catch(storeException $e) {
			throw new storeException($e->getMessage(), $e->getCode());
		}
	}
	
	private function sendConfirmationCode() {
		try {
			$size1 = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
			$saltString1 = mcrypt_create_iv($size1, MCRYPT_DEV_RANDOM);
			$link1 = $this->registry->getObject('hash')->create_hash($saltString1);
			
			$size2 = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
			$saltString2 = mcrypt_create_iv($size2, MCRYPT_DEV_RANDOM);
			$link2 = $this->registry->getObject('hash')->create_hash($saltString2);
			
			$code = crypt($link1,$link2);
			$this->data['confirm_code'] = $this->registry->getObject('hash')->create_hash($code);
			
			$confirmLink = 'shiplock.us/store/confirm/?q='.$link1.'&&p='.$link2;
			$mail = $this->registry->getObject('mail')->getMailObject();

			$this->registry->getObject('template')->buildFromTemplates('mail.html');
			$this->registry->getObject('template')->getPage()->addTag('name', $this->data['name']);
			$this->registry->getObject('template')->getPage()->addTag('confirm_link', $confirmLink);
			$this->registry->getObject('template')->parseOutput();
			
			$mail->From = 'confirm@shiplock.us';
			$mail->FromName = 'Shiplock Inc.';
			$subject = 'Confirmation of your account';
			$mail->addAddress($this->data['email'], $this->data['name']);
			$mail->addReplyTo('contact@shiplock.us', 'Shiplock Inc.');
			$mail->Body =  $this->registry->getObject('template')->getPage()->getContentToPrint();
			$mail->AltBody = "Dear {$this->data['name']} \n Confirm Your account by clicking on the link bellow\n {$confirmLink}";
			$mail->isHTML(true);
			if(!$mail->send()) {
	   			echo 'Message could not be sent.';
   				echo 'Mailer Error: ' . $mail->ErrorInfo;
   				exit;
			}
		} catch(storeException $e) {
			throw new storeException($e->getMessage(), $e->getCode());
		}
	}
}

?>
