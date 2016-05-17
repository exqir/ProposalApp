<?php
	require_once 'institut.php';

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
		* Checks if there already exists a proposal with the given $title and $enddate, return 0 if not
		* If there already exists such an entry, checks if on of those has the same institute given in $institute, returns 1 if thats the case
		**/
		public function checkProposalExistance(Proposal $proposal)
		{
			$title = $proposal->getTitle();
			$institut = $proposal->getInstitut()->getName();
			$enddate = $proposal->getEnddate();
			$query = "SELECT InstID, InstOptID FROM proposal WHERE Title=? AND Enddate=?";
			//STR_TO_DATE(?, '%Y-%m-%d')
			if($stmt = $this->mysqli->prepare($query))
			{
				$title = trim($title);
				$stmt->bind_param("ss", $title, $enddate);
				if($stmt->execute())
				{
					$stmt->store_result();
					$instId;
					$stmt->bind_result($instId, $instOptId);

					if($stmt->num_rows == 0)
					{
						echo "|" . $title . "| = |" . $enddate . "|</br>";
						$stmt->close();
						return 0;
					}
					else
					{
						if($proposal->getInstOpt() == 1)
						{
							echo "InstOpt: 1 </br>";
							$instOptional = $proposal->getInstitutOptional()->getName();
							$innerQuery = "SELECT Institute FROM institutes WHERE ID=?";
							while($stmt->fetch())
							{
								$tmp = 0;
								if($innerStmt = $this->mysqli->prepare($innerQuery))
								{
									$innerStmt->bind_param("i", $instId);
									if($innerStmt->execute())
									{
										$innerStmt->store_result();
										$instName;
										$innerStmt->bind_result($instName);
										while($innerStmt->fetch())
										{
											echo "|" . $instName . "| = |" . $institut . "|</br>";
											$output = strcmp($instName,$institut);
											echo $output;
											if(trim($instName) === trim($institut))
											{
												echo "Inst 1: Match </br>";
												if($scdStmt = $this->mysqli->prepare($innerQuery))
												{
													$scdStmt->bind_param("i",$instOptId);
													if($scdStmt->execute())
													{
														$scdStmt->store_result();
														$instOptName;
														$scdStmt->bind_result($instOptName);
														while($scdStmt->fetch())
														{
															echo $instOptName . " = " . $instOptional . "</br>";
															if(trim($instOptName) === trim($instOptional))
															{
																echo "Inst 2: Match </br>";
																$scdStmt->close();
																$innerStmt->close();
																$stmt->close();
																return 1;
															}
														}
													}
												}
											}
										}
										$innerStmt->close();
									}
								}
								if ( !$innerStmt )
								{
								    printf('errno: %d, error: %s', $mysqli->errno, $mysqli->error);
								    die;
								}
							}
							return 0;

						}

						$innerQuery = "SELECT Institute FROM institutes WHERE ID=?";
						while($stmt->fetch())
						{
							if($innerStmt = $this->mysqli->prepare($innerQuery))
							{
								$innerStmt->bind_param("i", $instId);
								if($innerStmt->execute())
								{
									$innerStmt->store_result();
									$instName;
									$innerStmt->bind_result($instName);
									while($innerStmt->fetch())
									{
										echo $instName . " = " . $institut . "</br>";
										if(trim($instName) === trim($institut))
										{
											$innerStmt->close();
											$stmt->close();
											return 1;
										}
									}
									$innerStmt->close();
								}
							}
							if ( !$innerStmt ) {
							    printf('errno: %d, error: %s', $mysqli->errno, $mysqli->error);
							    die;
							}
						}
						$innerStmt->close();
						$stmt->close();
						return 0;
					}
				}
			}
			if ( !$stmt ) {
			    printf('errno: %d, error: %s', $mysqli->errno, $mysqli->error);
			    die;
			}

		}

		/**
		* Checks if a $institut with the name already exists, returns a 0 if not otherwise returns a 1
		* and sets the ID and TypeID of the transmitted $institut to those from the db
		**/
		public function checkInstituteExistance(Institut &$institut)
		{
			$query = "SELECT ID, TypeID FROM institutes WHERE Institute=?";

			if($stmt = $this->mysqli->prepare($query))
			{
				$instName = $institut->getName();
				$stmt->bind_param("s",$instName);

				if($stmt->execute())
				{
					$stmt->store_result();
					$stmt->bind_result($id, $typeId);

					if($stmt->num_rows == 0)
					{
						$stmt->close();
						$this->saveInstitute($institut);
						return 0;
					}
					else if($stmt->num_rows == 1)
					{
						$stmt->fetch();
						$institut->setId($id);
						$institut->setTypeId($typeId);
						$stmt->close();

						return 1;
					}
					else
					{
						echo "Too many Insitutes";
						return 1;
					}
				}
			}
		}

		/**
		* Selects the $abbrev from the db matching the trasmitted $typeId, returns the abbrev when found otherwise returns an empty string
		**/
		public function checkType(int $typeId)
		{
			$query = "SELECT Abbrev FROM types WHERE ID=?";

			if($stmt = $this->mysqli->prepare($query))
			{
				$stmt->bind_param("i", $typeId);
				if($stmt->execute())
				{
					$stmt->store_result();
					$stmt->bind_result($abbrev);
					$stmt->fetch();
					if($stmt->num_rows != 1) return "";
					return $abbrev;
				}
			}
			if ( !$stmt ) {
			    printf('errno: %d, error: %s', $mysqli->errno, $mysqli->error);
			    die;
			}
		}

		public function saveInstitute(Institut $institut)
		{
			$instName = $institut->getName();

			$institut->setTypeId(0);

			$query = "SELECT TypeID, Keyword, Strict FROM keywords";

			if($stmt = $this->mysqli->prepare($query))
			{
				if($stmt->execute())
				{
					$stmt->store_result();
					$stmt->bind_result($typeId, $keyword, $strict);

					while($stmt->fetch())
					{
						if($strict == 1)
						{
							if(preg_match("/\b" . preg_quote($keyword, '/') . "\b/i", $instName)) $institut->setTypeId($typeId);
						}
						else
						{
							if(stripos($instName,$keyword) !== false) $institut->setTypeId($typeId);
						}
					}
					$stmt->close();
				}
			}

			$typeId = $institut->getTypeId();
			$abbrev = $this->checkType($typeId);
			$institut->setAbbrev($abbrev);

			$query = "INSERT INTO institutes (TypeID, Abbrev, Institute, City, State, Country) VALUES (?,?,?,?,?,?)";

			if($stmt = $this->mysqli->prepare($query))
			{
				$stmt->bind_param("isssss", $institut->getTypeId(), $institut->getAbbrev(), $institut->getName(), $institut->getCity(), $institut->getState(), $institut->getCountry());

				if($stmt->execute())
				{
					$instId = $this->mysqli->insert_id;
					$institut->setId($instId);
					$stmt->close();
					return 1;
				}
				$stmt->close();
				return 0;
			}
			if ( !$stmt ) {
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    die;
			}

		}

		public function saveProposal(Proposal $proposal)
		{
			$colums = "(";
			foreach(array_keys(DB_PROPOSAL) as $key)
			{
				if($key === end(array_keys(DB_PROPOSAL))) $colums .= $key;
				else $colums .= $key . ", ";
			}
			$colums .= ")";
			$values = "(";
			$i = 0;
			foreach(DB_PROPOSAL as $value)
			{
				if(++$i === count(DB_PROPOSAL)) $values .= $value;
				else $values .= $value . ", ";
			}
			$values .= ")";
			$a_params = array();
			$param_type = "";
			$n = count(DB_PARAM_TYPES);
			$a_param_var = Config::getParam($proposal);
			$a_param_types = DB_PARAM_TYPES;
			for($i = 0; $i < $n; $i++)
			{
				$param_type .= $a_param_types[$i];
			}
			$a_params[] = &$param_type;
			for($i = 0; $i < $n; $i++)
			{
					$a_params[] = &$a_param_var[$i];
			}

			$query = "INSERT INTO proposal " . $colums . " VALUES " . $values . "";

			if($stmt = $this->mysqli->prepare($query))
			{
				$addition = $proposal->getTitleAdditions();
				call_user_func_array(array($stmt,'bind_param'), $a_params);

				if($stmt->execute())
				{
					$stmt->close();
					return 1;
				}
				$stmt->close();
				return 0;
			}
			if ( !$stmt )
			{
			    printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
			    die;
			}
		}

		public function getProposals()
		{
			$query =
			"SELECT proposal.*, institutes.Abbrev AS inst_abbrev, institutes.Institute AS instName
			FROM proposal
			INNER JOIN institutes
			ON proposal.InstID = institutes.ID
			ORDER BY id
			DESC LIMIT 100";
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
			$sql->close();
		}

		public function getProposal($id)
		{
			$query =
			"SELECT proposal.*, institutes.Abbrev AS instAbbrev, institutes.Institute AS instName
			FROM proposal
			INNER JOIN institutes
			ON proposal.InstID = institutes.ID
			WHERE proposal.ID = $id";
			echo $query;
			if($stmt = $this->mysqli->query($query)) {
				return $stmt->fetch_assoc();
			} else {
				printf('errno: %d, error: %s', $this->mysqli->errno, $this->mysqli->error);
				die;
			}
			$sql->close();
		}
	}
?>
