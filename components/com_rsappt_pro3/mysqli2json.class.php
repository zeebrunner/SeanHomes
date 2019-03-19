<?php
/** 
* Filename: mysql2json.class.php 
* Purpose: Convert mysql resultset data into JSON(http://json.org) format
* Author: Adnan Siddiqi <kadnan@gmail.com> 
* License: PHP License 
* Date: Tuesday,June 21, 2006 
*
*/

/* Modified to work with Joomla and MySQLi */

class mysql2json{


 function getJSON($resultSet,$affectedRecords){
 $numberRows=0;
 $arrfieldName=array();
 $i=0;
 $json="";


	while ($i <$resultSet->field_count)  {
 		$meta = $resultSet->fetch_field();
		$arrfieldName[$i]=$meta->name;
		$i++;
 	}
	 $i=0;
	  $json="{\n\"data\": [\n";
	while ($i <$resultSet->num_rows)  {
		$row = $resultSet->fetch_row();	
		$i++;
		//print("Ind ".$i."-$affectedRecords<br>");
		$json.="{\n";
		for($r=0;$r < count($arrfieldName);$r++) {
			$json.="\"$arrfieldName[$r]\" :	\"$row[$r]\"";
			if($r < count($arrfieldName)-1){
				$json.=",\n";
			}else{
				$json.="\n";
			}
		}
		
		 if($i!=$affectedRecords){
		 	$json.="\n},\n";
		 }else{
		 	$json.="\n}\n";
		 }
	}
	$json.="]\n}";
	
	return $json;
 }

}
?>
