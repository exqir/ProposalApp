<?php

	/**
	* Provides functions to check and save entries via SQL Queries
	**/
	class SqlHandler
	{
		private $types;
		private $instituts;
		private $mysqli;

		/**
		* Establishes a connection to the db and sets the charset to utf8
		**/
		public function __construct($host, $user, $pw, $db_name)
		{

			$this->mysqli = new mysqli($host, $user, $pw, $db_name);
			if(mysqli_connect_errno())
			{
				printf("Connect failed: %s\n", mysqli_connect_error());
				exit();
			}
			if (!$this->mysqli->set_charset("utf8")) {
			    printf("Error loading character set utf8: %s\n", $this->mysqli->error);
			    exit();
			}
		}

		/**
		* Terminates the connection to the db
		**/
		public function closeConnection()
		{
			$this->mysqli->close();
		}

		/**
		*
		**/
		public function handleProposal(Proposal &$proposal){
			$proposalExistance = $this->checkProposalExistance($proposal);
			if($proposalExistance === 1) {
				// Proposal already exists
				printf("Proposal already exists");
				return 0;
			} else if ($proposalExistance === 0) {
				//Proposal doesn't exist
				printf("New proposal");
				$desc = $proposal->getLink();
				$proposal->setDescription($desc);
				$proposalResult = $this->saveProposal($proposal);
				// $organisation = $proposal->getOrganization();
				// $organizationExistance = $this->checkOrganizationExistance($organization);
				// if($organizationExistance === 1) {
				//
				// }
				if($proposalResult === 1) return 1;
				else return 0;
			} else return -1;
		}

		private function checkProposalExistance(Proposal &$proposal){
			$title = $proposal->getTitle();
			$organization = $proposal->getOrganization();
			$enddate = $proposal->getEnddate();
			$query = "SELECT `OrgID`, `OrgOptID` FROM proposal
			WHERE Title = ? AND Enddate = ?";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("ss",$title,$enddate);
				if($stmt->execute()) {
					$stmt->store_result();
					$stmt->bind_result($orgId,$orgOptId);
					if($stmt->num_rows === 0) {
						//Found no proposal with the given title and enddate
						echo "No such proposal found</br>";
						$res = 0;
					} else {
						//Found a proposal with the given title and enddate
						$stmt->fetch();
						echo "Found such proposal, checking Organization: ";
						$res = -3;
						if($proposal->getOrgOpt() === 1) {
							//The parsed propsal has an optional organization
							echo "Optional organization found:";
							if($this->checkProposalOrganizationConnection($orgId,$organization->getName()) === 1
							&& $this->checkProposalOrganizationConnection($orgOptId,$organization->getName()) === 1){
								//Both organizations match, it's the same proposal
								$res = 1;
							}
						} else {
								//The parsed proposal has only one organization
								echo "No optional organization found: ";
								echo "<br />OrgID: " . $orgId . "<br />";
								if($this->checkProposalOrganizationConnection($orgId,$organization->getName()) === 1) {
									//The organization matches, it's the same proposal
									$res = 1;
								}
							}
					}
					echo "CheckingProposalExistance: " . $res . "</br>";
					$stmt->close();
					return $res;
				} else return -2;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		private function checkProposalOrganizationConnection($id,$orgName){
			$query = "SELECT Name FROM organizations WHERE ID = ?";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("i",$id);
				if($stmt->execute()) {
					$stmt->store_result();
					$stmt->bind_result($orgNameList);
					$stmt->fetch();
					$orgNamesFromTable = explode(";",$orgNameList);
					echo $id . " : ";
					echo $orgNameList;
					var_dump($orgNamesFromTable);
					$res = 0;
					foreach ($orgNamesFromTable as $orgNameTable) {
						//Compares every organization name from the table with the name given as paramter
						if(trim($orgName) === trim($orgNameTable)) {
							//The name frome the table and the paramter name match, it's the same organization
							echo "Proposal, Organization combination found </br>";
							$res = 1;
						}
					}
					$stmt->close();
					echo $res . "</br>";
					return $res;
				} else return -2;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		private function saveProposal(Proposal &$proposal) {
			$organization = $proposal->getOrganization();
			$saveResponse = $this->saveOrganization($organization);
			if($proposal->getOrgOpt() === 1) {
				echo "saving Optional Organization: ";
				$optOrganization = $proposal->getOrganizationOptional();
				$optSaveResponse = $this->saveOrganization($optOrganization);
				echo $optOrganization->getId();
			}
			$pushResponse = $this->pushProposalToDb($proposal);
			if($saveResponse === 1 && $pushResponse === 1) return 1;
			else return 0;
		}

		private function saveOrganization(Organization &$organization) {
			$organizationExistance = $this->checkOrganizationExistance($organization);
			if($organizationExistance === 0) {
				//Organization doesn't exist
				echo "New Organization";
				$name = $organization->getName();
				$typeId = $this->checkType($name);
				if($typeId >= 0) $organization->setTypeId($typeId);
				$abbrev = $this->getAbbrev($typeId);
				if($abbrev !== -1) $organization->setAbbrev($abbrev);
				else $organization->setAbbrev(NULL);
				$state = $this->findStateForOrganization($name);
				if($state !== NULL) $organization->setState($state);
				if($organization->getCountry() === NULL || $organization->getCountry() === "") $organization->setCountry("Deutschland");
				$pushResponse = $this->pushOrganizationToDB($organization);
				return $pushResponse;
			} else if($organizationExistance === 1) {
				//Organization already exists
				echo "Organization already exists";
				echo $organization->getId() . "<br />";
				return 1;
				}
			else return -1;
		}

		private function checkOrganizationExistance(Organization &$organization){
			$name = $organization->getName();
			$query = "SELECT ID, TypeID, AliasOf FROM organizations WHERE Name = ?";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("s",$name);
				if($stmt->execute()) {
					$stmt->store_result();
					$stmt->bind_result($id,$typeId,$aliasId);
					if($stmt->num_rows === 0) {
						//Found no organization with the given name
						$res = 0;
					} else if($stmt->num_rows === 1){
						//Found a organization with the given name
						$stmt->fetch();
						if($aliasId !== NULL) {
							//Found organization is an alias of another organization
							//Sets the id to the id of the main organization
							//TODO Change name to the name of the main organization
							echo "WRONG TURN" . $id . " <br />";
							$organization->setId($aliasId);
							$organization->setTypeId($typeId);
							$res = 1;
						} else {
							//Found organization is the main organization
							echo "Setting ID to: " . $id . "<br />";
							$organization->setId($id);
							$organization->setTypeId($typeId);
							$res = 1;
						}
					} else $res = -3;
					$stmt->close();
					echo "checkOrganizationExistance Intern: " . $res . "<br />";
					echo "checkIntern ID: " . $organization->getId() . "<br />";
					return $res;
				} else return -2;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		private function checkType($name) {
			$query = "SELECT TypeID, Keyword, Strict FROM keywords";
			if($stmt = $this->mysqli->prepare($query)){
				if($stmt->execute()) {
					$stmt->store_result();
					$stmt->bind_result($typeId, $keyword, $strict);
					$res = 0;
					while($stmt->fetch()){
						if($strict === 1) {
							//Compares the name and keyword stricly per regex, only when keyword is a single word it matches
							if(preg_match("/\b" . preg_quote($keyword, '/') . "\b/i", $name)) $res = $typeId;
						}
						else {
							//Compares the name name and keyword, keyword can also be a substring of name
							if(stripos($name,$keyword) !== false) $res = $typeId;
						}
					}
					$stmt->close();
					return $res;
				} else return -2;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		private function getAbbrev(int $typeId) {
			$query = "SELECT Abbrev FROM types WHERE ID=?";
			if($stmt = $this->mysqli->prepare($query)){
				$stmt->bind_param("i", $typeId);
				if($stmt->execute()) {
					$stmt->store_result();
					$stmt->bind_result($abbrev);
					$stmt->fetch();
					if($stmt->num_rows != 1) $res = -1;
					//If only one abbrev is found, returns the abbrev
					else $res = $abbrev;
					$stmt->close();
					return $res;
				} else {
				    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				    return -1;
				}
			}
		}

		private function findStateForOrganization($organisationName) {
			$placeId = $this->getPlaceIdFromAPI($organisationName);
			if($placeId !== NULL) return $this->getStateForPlaceId($placeId);
			else return NULL;
		}

		private function getPlaceIdFromAPI($organizationName) {
			$searchUrl = "https://maps.googleapis.com/maps/api/place/textsearch/xml?query=" . urlencode($organizationName) . "&key=" . APIKEY;
			$s = file_get_contents($searchUrl);
			$dom = new DomDocument();
			$dom->loadXML($s);
			$xpath = new DOMXPath($dom);
			$place = $xpath->query("/PlaceSearchResponse/result/place_id");
			$placeId = $place->item(0)->nodeValue;
			return $placeId;
		}

		private function getStateForPlaceId($placeId) {
			$detailUrl = "https://maps.googleapis.com/maps/api/place/details/xml?placeid=" . $placeId . "&key=" . APIKEY;
			$s = file_get_contents($detailUrl);
			$dom = new DomDocument();
			$dom->loadXML($s);
			$xpath = new DOMXPath($dom);
			$componentList = $xpath->query("/PlaceDetailsResponse/result/address_component");
		  $state = "";
		  foreach ($componentList as $comp) {
		     $type = $comp->getElementsByTagName('type')->item(0)->nodeValue;
		     if(stripos($type,"administrative_area_level_1") !== false) {
		       $state = $comp->getElementsByTagName('long_name')->item(0)->nodeValue;
		     }
		  }
			return $state;
		}

		private function pushOrganizationToDB(Organization &$organization) {
			$query = "INSERT INTO organizations (TypeID, Abbrev, Name, City, State, Country) VALUES (?,?,?,?,?,?)";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("isssss", $organization->getTypeId(), $organization->getAbbrev(),
				$organization->getName(), $organization->getCity(),
				$organization->getState(), $organization->getCountry());
				if($stmt->execute()){
					$orgDbId = $this->mysqli->insert_id;
					$organization->setId($orgDbId);
					$stmt->close();
					$res = 1;
				} else $res = 0;
				return $res;
			} else {
					printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
					return -1;
			}
		}

		private function pushProposalToDb(Proposal &$proposal) {
			$colums = "(";
			foreach(array_keys(DB_PROPOSAL) as $key) {
				if($key === end(array_keys(DB_PROPOSAL))) $colums .= $key;
				else $colums .= $key . ", ";
			}
			$colums .= ")";
			$values = "(";
			$i = 0;
			foreach(DB_PROPOSAL as $value) {
				if(++$i === count(DB_PROPOSAL)) $values .= $value;
				else $values .= $value . ", ";
			}
			$values .= ")";
			$a_params = array();
			$param_type = "";
			$n = count(DB_PARAM_TYPES);
			$a_param_var = Config::getParam($proposal);
			$a_param_types = DB_PARAM_TYPES;
			for($i = 0; $i < $n; $i++) {
				$param_type .= $a_param_types[$i];
			}
			$a_params[] = &$param_type;
			for($i = 0; $i < $n; $i++) {
					$a_params[] = &$a_param_var[$i];
			}
			$query = "INSERT INTO proposal " . $colums . " VALUES " . $values . "";
			if($stmt = $this->mysqli->prepare($query)) {
				$addition = $proposal->getTitleAdditions();
				call_user_func_array(array($stmt,'bind_param'), $a_params);

				if($stmt->execute()){
					$stmt->close();
					return 1;
				}
				$stmt->close();
				return 0;
			} else {
					printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
					return -1;
			}
		}

		public function editProposal(Proposal $proposal)
		{
			$query = "UPDATE proposal SET
			Title = ?,
			OrgID = ?,
			OrgOptID = ?,
			Description = ?,
			W1 = ?,
			W2 = ?,
			W3 = ?,
			C1 = ?,
			C2 = ?,
			C3 = ?,
			Tenure = ?,
			Ass = ?,
			Raw = ?,
			subject_culture = ?,
			subject_area = ?,
			subject = ?
			WHERE id = ?";
			if($stmt = $this->mysqli->prepare($query)){
				$title = $proposal->getTitle();
				$orgId = $proposal->getOrganizationId();
				$orgOptId = $proposal->getOrganizationOptId();
				$desc = $proposal->getDescription();
				$titleAdditions = $proposal->getTitleAdditions();
				$raw = $proposal->raw;
				$subjectCulture = $proposal->getSubjectCulture();
				$subjectArea = $proposal->getSubjectArea();
				$subject = $proposal->getSubject();
				$id = $proposal->getId();
				$stmt->bind_param("siisiiiiiiiiiiiii",
				$title,
				$orgId,
				$orgOptId,
				$desc,
				$titleAdditions['W1'],
				$titleAdditions['W2'],
				$titleAdditions['W3'],
				$titleAdditions['C1'],
				$titleAdditions['C2'],
				$titleAdditions['C3'],
				$titleAdditions['Tenure'],
				$titleAdditions['Ass'],
				$raw,
				$subjectCulture,
				$subjectArea,
				$subject,
				$id);
					if($stmt->execute()) {
						//success
						$stmt->close();
						return 1;
					}
			}
		}

		public function getProposals()
		{
			$query =
			"SELECT proposal.*, organizations.Abbrev AS orgAbbrev, organizations.Name AS orgName, organizations.State AS State , organizations.Country AS Country ,subject_culture.Name AS Culture, subject_area.Name AS Area, subject.Name AS Subject
			FROM proposal
			INNER JOIN organizations
				ON proposal.OrgID = organizations.ID
      LEFT OUTER JOIN subject_culture
      	ON proposal.subject_culture = subject_culture.ID
      LEFT OUTER JOIN subject_area
      	ON proposal.subject_area = subject_area.ID
      LEFT OUTER JOIN subject
      	ON proposal.subject = subject.ID
			ORDER BY id
			DESC";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getProposal($id)
		{
			$query =
			"SELECT proposal.*, organizations.Abbrev AS orgAbbrev, organizations.Name AS orgName, organizations.State AS State, organizations.Country AS Country, subject_culture.Name AS Culture, subject_area.Name AS Area, subject.Name AS Subject
			FROM proposal
			INNER JOIN organizations
				ON proposal.OrgID = organizations.ID
      LEFT OUTER JOIN subject_culture
      	ON proposal.subject_culture = subject_culture.ID
      LEFT OUTER JOIN subject_area
      	ON proposal.subject_area = subject_area.ID
      LEFT OUTER JOIN subject
      	ON proposal.subject = subject.ID
			WHERE proposal.ID = $id";
			echo $query;
			if($stmt = $this->mysqli->query($query)) {
				return $stmt->fetch_assoc();
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getOrganizations()
		{
			$query =
			"SELECT * FROM organizations WHERE AliasOf IS NULL";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getOrganization($id)
		{
			$query =
			"SELECT * FROM organizations
			WHERE organizations.ID = $id";
			echo $query;
			if($stmt = $this->mysqli->query($query)) {
				return $stmt->fetch_assoc();
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function editOrganization(Organization $organization)
		{
			$query = "UPDATE organizations SET
			TypeID = ?,
			Abbrev = ?,
			Name = ?,
			City = ?,
			State = ?,
			Country = ?
			WHERE id = ?";
			if($stmt = $this->mysqli->prepare($query)){
				$typeId = $organization->getTypeId();
				$abbrev = $organization->getAbbrev();
				$name = $organization->getName();
				$city = $organization->getCity();
				$state = $organization->getState();
				$country = $organization->getCountry();
				$id = $organization->getId();
				$stmt->bind_param("isssssi",
				$typeId,
				$abbrev,
				$name,
				$city,
				$state,
				$country,
				$id);
					if($stmt->execute()) {
						//success
						$stmt->close();
						return 1;
					}
			}
		}

		public function mergeOrangization($firstId,$secondId) {
			$res1 = $this->switchValue($firstId,$secondId,"proposal","OrgID","i");
			$res2 = $this->switchValue($firstId,$secondId,"proposal","OrgOptID","i");
			//TODO change to setAlias($firstId,$secondId)
			//setting $firstId as AliasOf for $secondId
			$res3 = $this->setAliasOf($secondId,$firstId);
			if($res1 === 1 && $res2 === 1 && $res3 === 1) return 1;
			else return 0;
		}

		private function setAliasOf($id,$alias) {
			$query = "UPDATE organizations SET AliasOf = ? WHERE ID = ?";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("ii",$alias ,$id);
				if($stmt->execute()) {
					$stmt->close();
					return 1;
				} else return 0;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		public function getOrganizationsWithAlias($id) {
			$query =
			"SELECT * FROM organizations
			WHERE organizations.AliasOf = $id";
			echo $query;
			if($stmt = $this->mysqli->query($query)) {
				return $stmt->fetch_assoc();
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		private function deleteRow($id,$table) {
			$query = "DELETE FROM $table WHERE ID = ?";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param("i",$id);
				if($stmt->execute()) {
					$stmt->close();
					return 1;
				} else return 0;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		private function switchValue($newValue,$oldValue,$table,$columName,$columeType) {
			$query =
			"UPDATE $table
			SET $columName = ?
			WHERE $columName = ? ";
			if($stmt = $this->mysqli->prepare($query)) {
				$stmt->bind_param($columeType . $columeType,$newValue,$oldValue);
				if($stmt->execute()){
					$stmt->close();
					return 1;
				} else {
					return 0;
				}
			}
			else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		public function getOrganizationNames() {
			$query =
			"SELECT Name, State FROM organizations ORDER BY Name";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getOrganizationTypes() {
			$query = "SELECT ID, Abbrev FROM types ORDER BY ID";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getSubjectCultures() {
			$query =
			"SELECT ID, Name FROM subject_culture";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getSubjectAreas() {
			$query =
			"SELECT ID, Name, ParentID FROM subject_area";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function getSubjects() {
			$query =
			"SELECT ID, Name, ParentID FROM subject";
			if($stmt = $this->mysqli->query($query)){
				$res = array();
				while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
					$res[] = $row;
				}
				$stmt->free();
				return $res;
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$stmt->close();
		}

		public function saveSubjects(&$subjects) {
			$res = 0;
			foreach ($subjects as $sub) {
				$cultureID = $this->pushSubjectCultureToDB($sub);
				//echo "cultureID: " . $cultureID . "<br />";
				if($cultureID > 0) {
					$sub->setId($cultureID);
					$res += $this->saveSubjectChildren($sub->getSubjectGroup(),$cultureID);
					//echo "saveChildRes:" . $res . "<br />";
				}
				else return -1;
			}
			return $res;
		}

		private function saveSubjectChildren(&$subs, $parentID) {
			$res = 0;
			foreach ($subs as $sub) {
				$l = sizeof($sub->getSubjectGroup());
				//echo "length: " . $l . "<br />";
				if(sizeof($sub->getSubjectGroup()) > 0) {
					//echo "Subject_Area: " . $sub->getName() . "<br />";
					$pID = $this->pushSubjectChildToDB($sub,$parentID,"subject_area");
					//echo "Subject_Area ID: " . $pID . "<br />";
					if($pID > 0) {
						$sub->setId($pID);
						$res = $this->saveSubjectChildren($sub->getSubjectGroup(),$pID);
					}
				}
				else {
					//echo "Subject: " . $sub->getName() . "<br />";
					$pID = $this->pushSubjectChildToDB($sub, $parentID, "subject");
					if($pID > 0) $sub->setId($pID);
					$res += $pID;
				}
			}
			return $res;
		}

		private function pushSubjectCultureToDB(&$sub) {
			$existance = $this->checkSubjectExistence($sub->getName(),"subject_culture");
			if($existance === 0) {
				$query = "INSERT INTO subject_culture (Name) VALUES (?)";
				if($stmt = $this->mysqli->prepare($query)) {
				    $stmt->bind_param("s",$sub->getName());
				    if($stmt->execute()) {
							$parentID = $this->mysqli->insert_id;
			        $stmt->close();
			        return $parentID;
				    } else return 0;
				} else {
				    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				    return -1;
				}
			} else return $existance;
		}

		private function pushSubjectChildToDB(&$sub, $parentID, $table) {
			$existance = $this->checkSubjectExistence($sub->getName(),$table);
			//echo "childID: " . $existance . "<br>";
			if($existance === 0) {
				$query = "INSERT INTO $table (Name, ParentID) VALUES (?,?)";
				if($stmt = $this->mysqli->prepare($query)) {
				    $stmt->bind_param("si",$sub->getName(),$parentID);
				    if($stmt->execute()) {
							$parentID = $this->mysqli->insert_id;
			        $stmt->close();
							//echo "ParentID: " . $parentID ."<br />";
			        return $parentID;
				    } else return 0;
				} else {
				    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				    return -1;
				}
			} else return $existance;
		}

		private function checkSubjectExistence($subName,$table) {
			//echo "checking " . $subName . "<br />";
			$query = "SELECT ID FROM $table WHERE Name = ?";
			if($stmt = $this->mysqli->prepare($query)) {
			    $stmt->bind_param("s",$subName);
			    if($stmt->execute()) {
						$stmt->store_result();
						$stmt->bind_result($id);
						if($stmt->num_rows === 0) {
							//Found no subject with the given name
							//echo $table . " new subject: num_rows -> " . $stmt->num_rows . "<br>";
							$res = 0;
						} else {
						$stmt->fetch();
						$res = $id;
						}
						$stmt->close();
		        return $res;
			    } else return -2;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		public function historySubjects($domObject) {
			$query = "SELECT ID, Title, Description FROM proposal";
			if($stmt = $this->mysqli->prepare($query)) {
			    $stmt->bind_result($id,$title,$desc);
			    if($stmt->execute()) {
						$stmt->store_result();
						while($stmt->fetch()) {
							$proposal = new Proposal();
							$proposal->setId($id);
							$proposal->setTitle($title);
							$proposal->setDescriptionManually($desc);
							$parser = new Parser();
							$parser->gatherSubjects($domObject, $proposal);
							echo $proposal->getSubjectCulture();
							echo "<br />";
							echo "to" . $proposal->getId();
							$this->pushHistorySubjectsToDB($proposal);
						}
			      $stmt->close();
			      return 1;
			    } else return 0;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		private function pushHistorySubjectsToDB($proposal) {
			$query = "UPDATE proposal SET subject_culture = ?, subject_area = ?, subject = ? WHERE ID = ?";
			if($stmt = $this->mysqli->prepare($query)) {
			    $stmt->bind_param("iiii",$proposal->getSubjectCulture(),$proposal->getSubjectArea(),$proposal->getSubject(),$proposal->getId());
			    if($stmt->execute()) {
			        $stmt->close();
			        return 1;
			    } else return 0;
			} else {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    return -1;
			}
		}

		public function historyStates() {
			$query = "SELECT ID, Name FROM organizations WHERE ID > 400";
			if($stmt = $this->mysqli->prepare($query)) {
					$stmt->bind_result($id,$name);
					if($stmt->execute()) {
						$stmt->store_result();
						while($stmt->fetch()) {
							$state = $this->findStateForOrganization($name);
							$this->pushHistoryStatesToDB($id,$state);
						}
						$stmt->close();
						return 1;
					} else return 0;
			} else {
					printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
					return -1;
			}
		}

		private function pushHistoryStatesToDB($id, $name) {
			$query = "UPDATE organizations SET State = ? WHERE ID = ?";
			if($stmt = $this->mysqli->prepare($query)) {
					$stmt->bind_param("si",$name,$id);
					if($stmt->execute()) {
							$stmt->close();
							return 1;
					} else return 0;
			} else {
					printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
					return -1;
			}
		}
	}
?>
