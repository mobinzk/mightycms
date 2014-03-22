<?php

	class Email {

		public $to;
		public $subject;
		public $content;
		public $from		= 'mightyCMS';

		public function __construct(){
		}

		public function send(){
			
			$return = array();
			
			$to = $this->to;
			$subject = $this->subject;
			$content = stripslashes($this->content);
			$from = $this->from;
			
			if (is_array($to)){
				foreach ($to as $recipient){	
					$to .= ','.$recipient;
				}
				$to = substr($to, 1, strlen($to) - 1);
			}
									
			$message = $content;
			
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			
			// Additional headers
			$headers .= "From: $from" . "\r\n";
			
			// Mail it
			
			if (mail($to, $subject, $message, $headers)){
				$return['return'] = true;
			} else {
				$return['return'] = false;
			}	
			
			return $return;	
			
		}
		
	}

?>