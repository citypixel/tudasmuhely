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
   <h1>Étlap (rendelés ellenőrzés)</h1>
<?php
		$con = mysqli_connect("localhost", "root", "", "etterem");

		if (mysqli_connect_errno())
		{
	  		echo "MySQL hiba:" . mysqli_connect_error();
		}
		mysqli_set_charset($con,"utf8");
		$result = mysqli_query($con, "SELECT e.azonosito, e.elnevezes, f.tipnev FROM etlap AS e 
					                    LEFT JOIN fajta AS f ON e.tipus = f.tipus
										ORDER BY f.tipus");
	
		if(mysqli_num_rows($result) > 0)
		{
			echo '<table width="50%" height="50%"><tr>';
			$i=0;
			
			while($row = mysqli_fetch_array($result))
			{
				$i++;
				$azonosito = $row['azonosito'];
				$elnevezes = $row['elnevezes'];
			
    			echo '<td> <a href="' . $_SERVER['PHP_SELF'] . '?etel=' . $azonosito . '">' . $elnevezes . '</a> </td>';
    			
    			if ($i % 4 == 0)
    			{
    				echo "</tr><tr>";
    			}
		}
		echo "</table>";
	}

	
	
	
	if (isset($_GET['etel']))
	{
	    $result        = mysqli_query($con, "SELECT elnevezes FROM etlap where azonosito=" . $_GET['etel']);
	    $row = mysqli_fetch_array($result);
		$etelelnevezes = $row['elnevezes'];
	    
		$result = mysqli_query($con, "SELECT nev,szuksmenny,mertegys,keszlet FROM recept,anyag where recept.akod=anyag.akod and recept.azonosito=" . $_GET['etel']);
?>
        
		<br />
		<table width="300px">
		<tr><th colspan="3"><?php echo $etelelnevezes; ?></th></tr>
		<tr><th>Alapanyag</th><th>Mennyiség</th><th>Mértékegység</th></tr>
		
<?php	
		while ($row = mysqli_fetch_array($result))
		{
            $neve   = $row['nev'];
            $menny  = $row['szuksmenny'];
            $mertek = $row['mertegys'];
			
			//megnézzük, hogy van-e készleten az adott összetevő
					   if ($row['keszlet'] < $row['szuksmenny']){
						  $hiany[] = $row['nev'];
					   }
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

if ($hiany){
   echo '<h2>Az étel nem rendelhető!</h2>';
   echo 'Hiányzó összetevők: <br />';
   foreach ($hiany as $key=>$value){
	  echo $value.'<br/>';
   }
} else{
   if (isset($_GET['etel'])){
   echo '<h2>Az étel rendelhető!</h2>';
   }
}
?>
</body>
</html>