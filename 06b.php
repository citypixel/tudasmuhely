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
<?php
		$con = mysqli_connect("localhost", "root", "", "etterem");

		if (mysqli_connect_errno())
		{
	  		echo "MySQL hiba:" . mysqli_connect_error();
		}
		mysqli_set_charset($con,"utf8");
		$result = mysqli_query($con, "SELECT akod,nev FROM anyag order by nev");
	
		if(mysqli_num_rows($result) > 0)
		{
			echo '<h3>Alapanyagok:</h3>';
			echo '<table width="50%" height="50%"><tr>';
			$i=0;
			
			while($row = mysqli_fetch_array($result))
			{
				$i++;
				$akod = $row['akod'];
				$nev = $row['nev'];
			
    			echo '<td> <a href="' . $_SERVER['PHP_SELF'] . '?akod=' . $akod . '">' . $nev . '</a> </td>';
    			
    			if ($i % 4 == 0)
    			{
    				echo "</tr><tr>";
    			}
		}
		echo "</table>";
	}
	else
	{
		echo "Nincs felvéve alapanyag";
	}
	
	if (isset($_GET['akod']))
	{
		$kod=$_GET['akod'];

		$result = mysqli_query($con,"SELECT nev FROM anyag where akod=".$kod);
		$row = mysqli_fetch_array($result);
		$nev = $row['nev'];
		
		$sql = "SELECT etlap.azonosito,elnevezes FROM etlap,recept,anyag where etlap.azonosito=recept.azonosito and recept.akod=anyag.akod and anyag.akod=".$_GET['akod'];
		$result = mysqli_query($con,$sql);
		$rows = mysqli_num_rows($result);

		if($rows)
		{
			echo '<br>';
			echo '<table width="300px">';
			echo '<tr><th>Étel megnevezése</th></tr>';
			while ($sor = mysqli_fetch_array($result))
			{
			    $azonosito = $sor['azonosito'];
			    $elnevezes = $sor['elnevezes'];
?>
			    <tr>
    				<td>
<?php
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?akod=' . $kod . '&amp;etel=' . $azonosito . '">' . $elnevezes . '</a>';
?>
    				</td>
			    </tr>
<?php
			}
			echo '</table><br>';
		}
		else{
			echo "<br>Nem tartoznak a kiválasztott alapanyagokhoz ételek";
		}
	}
	
	if (isset($_GET['etel']))
	{
	    $result        = mysqli_query($con, "SELECT elnevezes FROM etlap where azonosito=" . $_GET['etel']);
	    $row = mysqli_fetch_array($result);
		$etelelnevezes = $row['elnevezes'];
	    
		$result = mysqli_query($con, "SELECT nev,szuksmenny,mertegys FROM recept,anyag where recept.akod=anyag.akod and recept.azonosito=" . $_GET['etel']);
?>
        
		
		<table width="300px">
		<tr><th colspan="3"><?php echo $etelelnevezes; ?></th></tr>
		<tr><th>Alapanyag</th><th>Mennyiség</th><th>Mértékegység</th></tr>
		
<?php	
		while ($row = mysqli_fetch_array($result))
		{
            $neve   = $row['nev'];
            $menny  = $row['szuksmenny'];
            $mertek = $row['mertegys'];
?>
            <tr>
                <td> <?php echo $neve;   ?> </td>
                <td> <?php echo $menny;  ?> </td>
                <td> <?php echo $mertek; ?> </td>
            </tr>
<?php
		}
		echo "</table><br>";
	}
	mysqli_close($con);
?>
</body>
</html>