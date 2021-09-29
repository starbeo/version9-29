<?php 

require_once '../../includes/phpass.php';
//require_once 'phpass.php';

	class DbOperations{

		private $con; 

		function __construct(){

			require_once dirname(__FILE__).'/DbConnect.php';

			$db = new DbConnect();

			$this->con = $db->connect();

		}
		public function userLogin($username, $pass){
			$user   = $this->getUserByUsername($username);
			if($user){
				$hasher = new PasswordHash(8, FALSE);
				if (!$hasher->CheckPassword($pass, $user['password'])) {
	        		return false;
	        	}
				return true;
			}
		}

		public function getUserByUsername($username){
			$stmt = $this->con->prepare("SELECT tblexpediteurs.*,tblvilles.name as 'ville' FROM tblexpediteurs,tblvilles WHERE tblexpediteurs.ville_id=tblvilles.id AND tblexpediteurs.email = '$username'");
			//$stmt->bind_param("s",$username);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}
		
		public function clientLogin($username, $pass){
			$user   = $this->getClientByUsername($username);
			if($user){
				$hasher = new PasswordHash(8, FALSE);
				if (!$hasher->CheckPassword($pass, $user['password'])) {
	        		return false;
	        	}
				return true;
			}
		}

		public function getClientByUsername($username){
			$stmt = $this->con->prepare("SELECT * FROM `tblexpediteurs` WHERE email = ?");
			$stmt->bind_param("s",$username);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}

		private function isUserExist($username, $email){
			$stmt = $this->con->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
			$stmt->bind_param("ss", $username, $email);
			$stmt->execute(); 
			$stmt->store_result(); 
			return $stmt->num_rows > 0; 
		}
		//Colis livré
		public function colislivre($id){
			$user   = $this->getcolislivre($id);
			if($user){
	        		return true;
			}
			return false;
		}
		public function getcolislivre($id){
			$stmt = $this->con->prepare("SELECT * FROM tblcolis WHERE status_id=2 AND tblcolis.id_expediteur = ?");
			$stmt->bind_param("s",$id);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}
		//Colis enattend
		public function colisencours($id){
			$user   = $this->getcolisenattend($id);
			if($user){
	        		return true;
			}
			return false;
		}
		public function getcolisenattend($id){
			$stmt = $this->con->prepare("SELECT * FROM tblcolis WHERE status_id=1 AND tblcolis.id_expediteur = ?");
			$stmt->bind_param("s",$id);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}
		
		/* Ajouté par Jabbour Esseddik le 22 09 2018 */
		public function staffLogin($username, $pass){
			$user   = $this->getStaffByUsername($username);
			if($user){
				$hasher = new PasswordHash(8, FALSE);
				if (!$hasher->CheckPassword($pass, $user['password'])) {
	        		return false;
	        	}
				return true;
			}
		}

		public function getStaffByUsername($username){
			$stmt = $this->con->prepare("SELECT * FROM tblstaff WHERE role=1 AND  email = '$username'");
			//$stmt->bind_param("s",$username);
			$stmt->execute();
			return $stmt->get_result()->fetch_assoc();
		}

		/* */
	}