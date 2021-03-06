<?php

	class Mighty_Activities {

		public function __construct() {
		
		}
		
		public function getAll($limit = '10') {
			$data = DBi::getAll("SELECT * FROM `mighty_activities` ORDER BY id DESC LIMIT $limit");

			if($data)
			return $data;
		}

		public function timeAgo($date){
		
			if(empty($date)) {
				return "No date provided";
			}
		 
			$periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
			$lengths         = array("60","60","24","7","4.35","12","10");
		 
			$now             = time();
			$unix_date         = $date;
		 
			   // check validity of date
			if(empty($unix_date)) {    
				return "Bad date";
			}
		 
			// is it future date or past date
			if($now > $unix_date) {    
				$difference     = $now - $unix_date;
				$tense         = "ago";
		 
			} else {
				$difference     = $unix_date - $now;
				$tense         = "from now";
			}
		 
			for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
				$difference /= $lengths[$j];
			}
		 
			$difference = round($difference);
		 
			if($difference != 1) {
				$periods[$j].= "s";
			}
		 
			return "$difference $periods[$j] {$tense}";		
		
		}

		public function log($message, $action){
		    
		    $userid = Mighty::Auth()->userId();
		    $user   = Mighty::Auth()->getUser()->username;
		    $time = time();

		    DBi::query("INSERT INTO `mighty_activities` SET `user` = '".$user."', `userid` = '".$userid."', `message` = '".dbi::mysqli()->real_escape_string($message)."', `action` = '".$action."', `time` = '".$time."' ");
		}	


	}

?>