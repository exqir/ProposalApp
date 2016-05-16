<?php
	header("Content-Type: text/html; charset=utf-8");
	$sql = new mysqli('dd20706.kasserver.com', 'd0214298', 'c77nX5zRd8N72amo', 'd0214298');
	if(mysqli_connect_errno())
	{
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
	if (!$sql->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $sql->error);
	    exit();
	}
	
	$query = "SELECT ID, InstID, TypeID, InstOptID, TypeOptID, Title, Description, Ass, W1, W2, W3, C1, C2, C3, C4, Tenure, Enddate, Link, SaveTime FROM proposal";
	
	echo "<table style='border:1px solid #000'>";
	echo "<tr style='border:1px solid #000'>";
	echo "<td style='border:1px solid #000'> ID: </td>";
	echo "<td style='border:1px solid #000'> Institut: </td>";
	echo "<td style='border:1px solid #000'> Type </td>";
	echo "<td style='border:1px solid #000'> InstOpt </td>";
	echo "<td style='border:1px solid #000'> InstType</td>";
	echo "<td style='border:1px solid #000'> Title </td>";
	echo "<td style='border:1px solid #000'> Description </td>";
	echo "<td style='border:1px solid #000'> Ass </td>";
	echo "<td style='border:1px solid #000'> W1 </td>";
	echo "<td style='border:1px solid #000'> W2 </td>";
	echo "<td style='border:1px solid #000'> W3 </td>";
	echo "<td style='border:1px solid #000'> C1 </td>";
	echo "<td style='border:1px solid #000'> C2 </td>";
	echo "<td style='border:1px solid #000'> C3</td>";
	echo "<td style='border:1px solid #000'> C4</td>";
	echo "<td style='border:1px solid #000'> Tenure </td>";
	echo "<td style='border:1px solid #000'> Enddate </td>";			
	echo "<td style='border:1px solid #000'> Link </td>";
	echo "<td style='border:1px solid #000'> SaveTime </td>";
	echo "</tr>";
	
	if($stmt = $sql->prepare($query))
	{
		if($stmt->execute())
		{
			$stmt->store_result();
			$stmt->bind_result($id,$instId,$typeId,$instOptId,$typeOptId,$title,$desc,$ass,$w1,$w2,$w3,$c1,$c2,$c3,$c4,$tenure,$enddate, $link, $saveTime);
			
			while($stmt->fetch())
			{
				$innerQueryOne = "SELECT Institute FROM institutes WHERE ID=?";
				
				if($innerStmtOne = $sql->prepare($innerQueryOne))
				{
					$innerStmtOne->bind_param("i", $instId);
					if($innerStmtOne->execute())
					{
						$innerStmtOne->store_result();
						$innerStmtOne->bind_result($institute);
						$innerStmtOne->fetch();
					}
					$innerStmtOne->close();
				}
				$innerQueryTwo = "SELECT Bezeichnung FROM types WHERE ID=?";
				
				if($innerStmtTwo = $sql->prepare($innerQueryTwo))
				{
					$innerStmtTwo->bind_param("i", $typeId);
					if($innerStmtTwo->execute())
					{
						$innerStmtTwo->store_result();
						$innerStmtTwo->bind_result($type);
						$innerStmtTwo->fetch();
					}
					$innerStmtTwo->close();
				}

				$innerQueryOne = "SELECT Institute FROM institutes WHERE ID=?";
				
				if($innerStmtOne = $sql->prepare($innerQueryOne))
				{
					$innerStmtOne->bind_param("i", $instOptId);
					if($innerStmtOne->execute())
					{
						$innerStmtOne->store_result();
						$innerStmtOne->bind_result($instituteOpt);
						if($innerStmtOne->num_rows == 0) $instituteOpt = "";
						$innerStmtOne->fetch();
					}
					$innerStmtOne->close();
				}
				$innerQueryTwo = "SELECT Bezeichnung FROM types WHERE ID=?";
				
				if($innerStmtTwo = $sql->prepare($innerQueryTwo))
				{
					$innerStmtTwo->bind_param("i", $typeOptId);
					if($innerStmtTwo->execute())
					{
						$innerStmtTwo->store_result();
						$innerStmtTwo->bind_result($typeOpt);
						if($innerStmtTwo->num_rows == 0) $typeOpt = "";
						$innerStmtTwo->fetch();
					}
					$innerStmtTwo->close();
				}
				
				echo "<tr style='border:1px solid #000'>";
				echo "<td style='border:1px solid #000'>" . $id . "</td>";
				echo "<td style='border:1px solid #000'>" . $institute . "</td>";
				echo "<td style='border:1px solid #000'>" . $type . "</td>";
				echo "<td style='border:1px solid #000'>" . $instituteOpt . "</td>";
				echo "<td style='border:1px solid #000'>" . $typeOpt . "</td>";
				echo "<td style='border:1px solid #000'>" . $title . "</td>";
				echo "<td style='border:1px solid #000'>" . substr($desc,0,200) . " ...</td>";
				echo "<td style='border:1px solid #000'>" . $ass . "</td>";
				echo "<td style='border:1px solid #000'>" . $w1 . "</td>";
				echo "<td style='border:1px solid #000'>" . $w2 . "</td>";
				echo "<td style='border:1px solid #000'>" . $w3 . "</td>";
				echo "<td style='border:1px solid #000'>" . $c1 . "</td>";
				echo "<td style='border:1px solid #000'>" . $c2 . "</td>";
				echo "<td style='border:1px solid #000'>" . $c3 . "</td>";
				echo "<td style='border:1px solid #000'>" . $c4 . "</td>";
				echo "<td style='border:1px solid #000'>" . $tenure . "</td>";
				echo "<td style='border:1px solid #000'>" . $enddate . "</td>";			
				echo "<td style='border:1px solid #000'> <a href='https://www.academics.de" . $link . "'>Link</td>";
				echo "<td style='border:1px solid #000'>" . $saveTime . "</td>";
				echo "</tr>";
			}
		}
		$stmt->close();
	}
	if ( !$stmt ) 
	{
	    printf('errno: %d, error: %s', $sql->errno, $sql->error);
	    die;
	}	
	
	echo "</table>";
	$sql->close();	
?>