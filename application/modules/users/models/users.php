<?php

	class Users {
		
		public static function getAll(){
			
			return DBi::getAll("SELECT 
									id, 
									firstname, 
									surname,
									username,
									position 
								FROM 
									mighty_users 
								WHERE 
									hidden = 0 
								ORDER BY id ASC");
		}

		public function delete() {
			extract($_POST);

			$user = DBi::getRow("SELECT `firstname`,`surname` FROM `mighty_users` WHERE id = '$id'");
			
			DBi::query("DELETE FROM `mighty_users` WHERE `id` = '$id'");

			Mighty::activities()->log('deleted a user account ('.stripslashes($user->firstname).' '.stripslashes($user->surname).')', 'delete');
			$response['ui_alert'] = (object) array(
				'type' 		=> 'success',
				'heading' 	=> 'Success!',
				'message' 	=> '<strong>"'.stripslashes($user->firstname).' '.stripslashes($user->surname).'"</strong> has been Deleted.',
			);

			return (object) $response;
		}

		public function addNewUser() {
			extract($_POST);

			if($firstname && $email) {
				$password = $this->passGenerator();

				$userid = DBi::query("INSERT INTO 
				    				`mighty_users` 
				    			SET 
				    				`firstname` 	= '".dbi::mysqli()->real_escape_string($firstname)."', 
				    				`surname` 		= '".dbi::mysqli()->real_escape_string($surname)."', 
				    				`username` 		= '".dbi::mysqli()->real_escape_string($email)."', 
				    				`salt`	 		= '".$password['salt']."',
				    				`password` 		= '".$password['hash']."'
				    			");

				 foreach ($_POST as $key => $p) {
				 	${$p} = $p;
				 }

				 // Set Permissions
	 			$add_page 			= ($add_page) ? '1' : '0';
				$delete_page 		= ($delete_page) ? '1' : '0';
				$publish_page 		= ($publish_page) ? '1' : '0';
				$edit_page 		= ($edit_page) ? '1' : '0';
				$sort_page 		= ($sort_page) ? '1' : '0';

				$add_article 		= ($add_article) ? '1' : '0';
				$delete_article 	= ($delete_article) ? '1' : '0';
				$publish_article 	= ($publish_article) ? '1' : '0';
				$edit_article 		= ($edit_article) ? '1' : '0';

				$add_filemanager 	= ($add_filemanager) ? '1' : '0';
				$delete_filemanager = ($delete_filemanager) ? '1' : '0';

				$add_user 			= ($add_user) ? '1' : '0';
				$delete_user 		= ($delete_user) ? '1' : '0';
				$edit_user 		= ($edit_user) ? '1' : '0';
				$edit_the_user 	= ($edit_the_user) ? '1' : '0';

				$edit_snippets 	= ($edit_snippets) ? '1' : '0';

				 // Insert new permission sets
				 DBi::query("INSERT INTO
				 						`permissions`
				 					SET
				 						`add_page` 			= '".$add_page."',
				 						`delete_page` 		= '".$delete_page."',
				 						`publish_page` 		= '".$publish_page."',
				 						`edit_page` 		= '".$edit_page."',
				 						`sort_page` 		= '".$sort_page."',

				 						`add_article` 		= '".$add_article."',
				 						`delete_article` 	= '".$delete_article."',
				 						`publish_article` 	= '".$publish_article."',
				 						`edit_article` 		= '".$edit_article."',

				 						`add_filemanager` 	= '".$add_filemanager."',
				 						`delete_filemanager` = '".$delete_filemanager."',

				 						`add_user` 			= '".$add_user."',
				 						`delete_user` 		= '".$delete_user."',
				 						`edit_user` 		= '".$edit_user."',
				 						`edit_the_user` 	= '".$edit_the_user."',

				 						`edit_snippets` 	= '".$edit_snippets."',

				 						`userid` 			= '".$userid['id']."'
				 			");

 				Mighty::activities()->log('added a new user ('.stripslashes($firstname).' '.stripslashes($surname).')', 'add');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'New user account has been added <a href="/mightycms/users">Return to Users</a> - 
									<form method="post" action="/mightycms/users/edit">
										<input type="hidden" value="edit" name="action">
										<input type="hidden" value="'.$userid['id'].'" name="id">
										<button type="submit">Edit <strong>"'.stripslashes($firstname).' '.stripslashes($surname).'"</strong></button>
									</form>',
				);


				// Notify User
				$Semail = new Email;
				$Semail->to = $email;
				$Semail->from = 'mightyCMS <no_reply@'.$_SERVER['SERVER_NAME'].'>';
				$Semail->subject = 'Account created';
				
				$content = "<h1>Account created</h1>";
				$content .= "<p style='line-height:21px'>Dear ".ucfirst(stripslashes($firstname)).",<br />";
				$content .= "An account has been created for you to access mightyCMS, your login details are as follows:</p>";
				$content .= "<p style='line-height:21px'><strong>Username:</strong> ".stripslashes($email)."<br />";
				$content .= "<strong>Password:</strong> ".stripslashes($password['original'])."</p>";
				$content .= "<p class='rule'>&nbsp;</p>";
				$content .= "<h2>Gettings started</h2>";
				$content .= "<p style='line-height:21px'>You can <a href='".'http://'.$_SERVER['SERVER_NAME'].'/mightycms'."'>login immediately</a>, once logged in we recommend you do the following:</p>";
				$content .= "<ul style='line-height:21px'>
				    			<li>Login to mightyCMS</li>
				    			<li>Change your password to something a more memorable</li>
				    			<li>Keep your new password safe</li>
				    			<li>Delete this email</li>
				    		</ul>";
				
				$Semail->content = $content;
				$Semail->send();

			}

			return (object) $response;
		}

		public function editUser() {
			extract($_POST);

			if($firstname && $email) {
				 DBi::query("UPDATE 
				    				`mighty_users` 
				    			SET 
				    				`firstname` 	= '".dbi::mysqli()->real_escape_string($firstname)."', 
				    				`surname` 		= '".dbi::mysqli()->real_escape_string($surname)."', 
				    				`username` 		= '".dbi::mysqli()->real_escape_string($email)."'
				    			WHERE
				    				id = '$id'
				    			");

				 foreach ($_POST as $key => $p) {
				 	${$p} = $p;
				 }

				 // Set Permissions
	 			$add_page 			= ($add_page) ? '1' : '0';
				$delete_page 		= ($delete_page) ? '1' : '0';
				$publish_page 		= ($publish_page) ? '1' : '0';
				$edit_page 		= ($edit_page) ? '1' : '0';
				$sort_page 		= ($sort_page) ? '1' : '0';

				$add_article 		= ($add_article) ? '1' : '0';
				$delete_article 	= ($delete_article) ? '1' : '0';
				$publish_article 	= ($publish_article) ? '1' : '0';
				$edit_article 		= ($edit_article) ? '1' : '0';

				$add_filemanager 	= ($add_filemanager) ? '1' : '0';
				$delete_filemanager = ($delete_filemanager) ? '1' : '0';

				$add_user 			= ($add_user) ? '1' : '0';
				$delete_user 		= ($delete_user) ? '1' : '0';
				$edit_user 		= ($edit_user) ? '1' : '0';
				$edit_the_user 	= ($edit_the_user) ? '1' : '0';

				$edit_snippets 	= ($edit_snippets) ? '1' : '0';

				 // Delete existing permissions
				 DBi::query("DELETE FROM `permissions` WHERE `userid` = '$id'");

				 // Insert new permission sets
				 DBi::query("INSERT INTO
				 						`permissions`
				 					SET
				 						`add_page` 			= '".$add_page."',
				 						`delete_page` 		= '".$delete_page."',
				 						`publish_page` 		= '".$publish_page."',
				 						`edit_page` 		= '".$edit_page."',
				 						`sort_page` 		= '".$sort_page."',

				 						`add_article` 		= '".$add_article."',
				 						`delete_article` 	= '".$delete_article."',
				 						`publish_article` 	= '".$publish_article."',
				 						`edit_article` 		= '".$edit_article."',

				 						`add_filemanager` 	= '".$add_filemanager."',
				 						`delete_filemanager` = '".$delete_filemanager."',

				 						`add_user` 			= '".$add_user."',
				 						`delete_user` 		= '".$delete_user."',
				 						`edit_user` 		= '".$edit_user."',
				 						`edit_the_user` 	= '".$edit_the_user."',

				 						`edit_snippets` 	= '".$edit_snippets."',

				 						`userid` 			= '$id'
				 			");

				Mighty::activities()->log('updated a user ('.stripslashes($firstname).' '.stripslashes($surname).')', 'update');
				$response['ui_alert'] = (object) array(
					'type' 		=> 'success',
					'heading' 	=> 'Success!',
					'message' 	=> 'Your changes have been saved. <a href="/mightycms/users">Return to Users</a>',
				);
			}

			if($password && $confirm_password && $password === $confirm_password) {

				$password = $this->passGenerator($password);
				DBi::query("UPDATE 
				    				`mighty_users` 
				    			SET 
				    				`salt`	 		= '".$password['salt']."',
				    				`password` 		= '".$password['hash']."'
				    			WHERE
				    				id = '$id'
				    			");
			}

			return (object) $response;
		}

		public function getUser($id) {
			return DBi::getRow("SELECT 
									id, 
									firstname, 
									surname,
									username as email,
									position  
								FROM 
									`mighty_users` 
								WHERE 
									`id` = $id");
		}

		public function passGenerator($password = '') {

			if(!$password) {
				// Generate new password
				$string = md5(uniqid(rand(), true));
				$password = substr($string, 0, 8);
			}
			
			// Generate salt
			$string = md5(uniqid(rand(), true));
			$salt = substr($string, 0, 3);
			
			// Generate hash
			$hash = hash('sha256', $salt . hash('sha256', $password));

			return array('salt' => $salt, 'hash' => $hash, 'original' => $password);
		}

		public function getPermissions($userid = '') {
			if(!$userid) {
				$userid = Mighty::Auth()->userId();
			}

			return DBi::getRow("SELECT 
									*  
								FROM 
									`permissions` 
								WHERE 
									`userid` = $userid");
		}
		
	}

?>