<?php
    $sql = mysqli_connect('localhost', 'root', '');
    $sql->select_db('etterem');
    $sql->set_charset("utf8");
    $akod = false;
    $azonosito = false;
    if(isset($_GET['azonosito'])){
        $azonosito = $_GET['azonosito'];
    }
?>
<html>
<head>
    <title>Schőn Péter</title>
</head>
<body>
   <h1>Beszerzések listázása</h1>
    <form name="idoszak">
	   <label>Időszak:</label>
	    <input type="text" name="tol" style="width: 80px;" value="<?php echo date("Y-m-d");?>">-
		<input type="text" name="ig" style="width: 80px;" value="<?php echo date("Y-m-d");?>"><br />
		<label>Sorrend:</label>
		<input type="radio" name="sorrend" value="datum" checked="checked">dátum
		<input type="radio" name="sorrend" value="anyag">anyag
		<br />
        <input type="submit" value="Listáz">
    </form>
   <table>
	  
<?php

if (isset($_GET[tol])){
   $tol = $_GET[tol];
   $ig = $_GET[ig];
   if ($_GET['sorrend'] == "anyag") {$sorrend = 'a.nev';}
   if ($_GET['sorrend'] == "datum") {$sorrend = 'b.datum';}
   
			echo '<tr><th>Dátum</th><th>Anyag</th><th>Mennyiség</th><th>Beszerzési ár</th></tr>';
             $beszerzes_query = $sql->query("SELECT b.akod, b.datum, b.bear, b.menny, a.nev, a.mertegys FROM beszerzes AS b 
											 LEFT JOIN anyag AS a ON a.akod = b.akod 
											 WHERE b.datum >= '2010-01-01' AND b.datum <= '2017-01-01' 
											 ORDER BY ".$sorrend);
            echo $sql->error;
			 while ($beszerz = $beszerzes_query->fetch_assoc()) {
                printf("<tr><td>%s</td><td>%s</td><td>%s %s</td><td>%s Ft</td></tr>\n", $beszerz['datum'], $beszerz['nev'], $beszerz['menny'], $beszerz['mertegys'], $beszerz['bear']);
            }
}
        ?>
	  </table>
</body>
</html>