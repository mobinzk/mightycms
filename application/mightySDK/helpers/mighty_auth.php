<?php

	class Mighty_Auth {

		public function __construct() {
		
		}

		public function login($username, $password, $remember=''){

			$user = $this->checkUser($username, $password);

			if(isset($user->id)) {
				$data['result'] = true;
				$_SESSION['uid'] = $user->id;

				Mighty::activities()->log('logged in.', 'login');

			} else {
				$data['message'] = 'The username or password you entered is incorrect.'; 
				$data['result'] = false;

				Mighty::activities()->log('Login attempt failed for username <strong>"'.dbi::mysqli()->real_escape_string($username).'"</strong> from <strong>'.$_SERVER['REMOTE_ADDR'].'</strong>', 'login-failure');
			}

			return (object) $data;

		}

		public function forgotten($email) {
			$user = DBi::getRow("SELECT 
									`username`
								FROM
									`mighty_users`
								WHERE
									`username` = '".dbi::mysqli()->real_escape_string($email)."'
								");
			if(!$user) {
				$data['result'] = false;
				$data['message'] = 'Sorry! We couldn\'t find a user with that Email address.';
			} else {
				$data['result'] = true;
				$data['message'] = 'We\'ve sent you an email containing your new password.';

				$password = Mighty::Users()->passGenerator();
				
				DBi::query("UPDATE 
								`mighty_users` 
							SET 
								`salt` = '".dbi::mysqli()->real_escape_string($password['salt'])."', 
								`password` = '".dbi::mysqli()->real_escape_string($password['hash'])."' 
							WHERE 
								`username` = '".dbi::mysqli()->real_escape_string($email)."'");
				
				$Semail = new Email;
				$Semail->to = $email;
				$Semail->subject = 'Your new password';
				
				$content = "<h1>Your new mightyCMS password</h1>";
				$content .= "<p>Your new password is: <strong>".stripslashes($password['original'])."</strong></p>";
				$content .= "<p class='rule'>&nbsp;</p>";
				$content .= "<h2>Didn't make this request?</h2>";
				$content .= "<p>If you did not request this password reset please take the following actions as precaution:</p>";
				$content .= "<ul>
								<li>Make a note of your new password</li>
								<li>Login to your account at the earliest convenience</li>
								<li>Change your password to something a little more memorable</li>
							</ul>";
				
				$Semail->content = $content;
				$Semail->send();

				Mighty::activities()->log('<strong>"'.dbi::mysqli()->real_escape_string($email).'"</strong> reset the password.', 'reset');

			}
			return (object) $data;
		}

		protected function checkUser($username = '', $password = ''){
			$user = DBi::getRow("SELECT salt FROM `mighty_users` WHERE `username` = '".dbi::mysqli()->real_escape_string($username)."'");

			if($user) {
				$password = hash('sha256', $user->salt . hash('sha256', dbi::mysqli()->real_escape_string($password)));
				return DBi::getRow("SELECT id FROM `mighty_users` WHERE `username` = '".dbi::mysqli()->real_escape_string($username)."' AND `password` = '".$password."'");
			}
		}

		public function logout(){
			Mighty::activities()->log('logged out.', 'logout');

			session_destroy();
			unset($_SESSION['uid']);
			if (!isset($_SESSION['uid'])){
				return true;
			} else {
				return false;
			}
		}
		
		public function userId(){
			if ($this->isLoggedIn()){
				return $_SESSION['uid'];
			} else {
				return false;
			}
		}

		public function getUser(){
			
			if ($this->isLoggedIn()){
				return DBi::getRow("SELECT `username` FROM `mighty_users` WHERE `id` = '".$this->userId()."'");
			}
		}

		public function isLoggedIn(){
			if (isset($_SESSION['uid']) && is_numeric($_SESSION['uid'])){
				return true;
			} else {
				return false;
			}
		}

	}

?>