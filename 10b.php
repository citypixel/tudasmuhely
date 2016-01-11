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
   <h1>Anyagbeszerzés</h1>
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
	
	if (isset($_GET['akod']))
	{
	   if (!isset($_GET['beszerzes'])){
		$akod=$_GET['akod'];

		$result = mysqli_query($con, "SELECT akod, nev, mertegys, keszlet FROM anyag WHERE akod=$akod");
		$row = mysqli_fetch_array($result);
		$nev = $row['nev'];
		$keszlet = $row['keszlet'];
		$mertegys = $row['mertegys'];
		
		$sql = "SELECT etlap.azonosito,elnevezes FROM etlap,recept,anyag where etlap.azonosito=recept.azonosito and recept.akod=anyag.akod and anyag.akod=".$_GET['akod'];
		$result = mysqli_query($con,$sql);
		$rows = mysqli_num_rows($result);
			echo '<br>';
			echo '<table width="300px">';
			echo '<tr><th>'.$nev.' beszerzés</th></tr>';
			echo '<tr><td>Készleten: '.$keszlet.' '.$mertegys.'</td></tr>';
			echo '<tr><td><form><label>Mennyiség:</label>
				  <input type="hidden" name="akod" value="'.$akod.'">
				  <input type="text" name="beszerzes" value=""> '.$mertegys.'<br />
				  <input type="submit" value="Rögzít"></form></td></tr>';
			echo '</table><br>';
	   }
	}
	
	if (isset($_GET['beszerzes']))
	{
	   $beszerzes=$_GET['beszerzes'];
	   $akod=$_GET['akod'];
	   $sql = "SELECT nev, keszlet, mertegys, egysar FROM anyag WHERE akod = $akod";
       $result = mysqli_query($con,$sql);
	   $row = mysqli_fetch_array($result);
       $keszlet = $row['keszlet'];
	   $mertegys = $row['mertegys'];
	   $nev = $row['nev'];
	   $egysar = $row['egysar'];
	   $uj_keszlet = ($keszlet + $beszerzes);
	   $bear = $beszerzes * $egysar;
				
         echo '<h2>'.$nev.' beszerzés</h2>';
		 echo 'Eredeti készlet: '. $keszlet. ' '.$mertegys.'<br />';
		 echo 'Beszerzés: '. $beszerzes. ' '.$mertegys.' ('.$bear.' Ft)<br />';
		 echo 'Új készlet: '. $uj_keszlet. ' '.$mertegys.'<br />';
		   
         $anyag_nev_query = mysqli_query($con, 'UPDATE anyag SET keszlet = '.$uj_keszlet.' WHERE akod = '.$akod);
               
		 $datum = date("Y-m-d"); 
				
		 $anyag_nev_query = mysqli_query($con, "INSERT INTO beszerzes (akod, datum, bear, menny) VALUES ($akod, '$datum', $bear, $beszerzes)");
	}
	
	mysqli_close($con);
?>
</body>
</html>