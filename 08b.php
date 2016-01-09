<!DOCTYPE html>
<head>
	<meta charset="utf-8" />
	
	<title>6. feladat</title>
	
	<style>
		table,th,td
		{
		border:1px solid black;
		border-collapse:collapse;
		padding: 3px;
		}
	</style>
</head>
<body>
   <h1>Étlap</h1>
<?php
		$con = mysqli_connect("localhost", "root", "", "etterem");

		if (mysqli_connect_errno())
		{
	  		echo "MySQL hiba:" . mysqli_connect_error();
		}
		mysqli_set_charset($con,"utf8");
		$result = mysqli_query($con, "SELECT e.azonosito, e.elnevezes, f.tipnev, f.tipus FROM etlap AS e 
					                    LEFT JOIN fajta AS f ON e.tipus = f.tipus
										GROUP BY f.tipus ORDER BY f.tipus");
	
		if(mysqli_num_rows($result) > 0)
		{
			echo '<table width="50%" height="50%"><tr>';
			$i=0;
			
			while($row = mysqli_fetch_array($result))
			{
				$i++;
				$tipus = $row['tipus'];
				$tipnev = $row['tipnev'];
			
    			echo '<td> <a href="' . $_SERVER['PHP_SELF'] . '?tipus=' . $tipus . '">' . $tipnev . '</a> </td>';
    			
    			if ($i % 4 == 0)
    			{
    				echo "</tr><tr>";
    			}
		}
		echo "</table>";
	}

	
	
	
	if (isset($_GET['tipus']))
	{
	    $result        = mysqli_query($con, "SELECT tipnev FROM fajta where tipus=" . $_GET['tipus']);
	    $row = mysqli_fetch_array($result);
		$etelelnevezes = $row['tipnev'];
	    
		$result = mysqli_query($con, "SELECT elnevezes FROM etlap WHERE tipus=" . $_GET['tipus']);
?>
        
		<br />
		<table width="300px">
		<tr><th colspan="3"><?php echo $etelelnevezes . ' ételek'; ?></th></tr>
		<tr><th>Megnevezés</th></tr>
		
<?php	
		while ($row = mysqli_fetch_array($result))
		{
            $elnevezes   = $row['elnevezes'];
            
?>
            <tr>
                <td> <?php echo $elnevezes;   ?> </td>
            </tr>
<?php
		}
		echo "</table><br>";
	}
	mysqli_close($con);
?>
</body>
</html>