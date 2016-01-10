<?php
session_start();
if($_GET['torol']== '1'){
   unset($_SESSION['kosar']);
}
    $sql = mysqli_connect('localhost', 'root', '');
    $sql->select_db('etterem');
    $sql->set_charset("utf8");
    $akod = false;
    $azonosito = false;
    if(isset($_GET['azonosito'])){
        $azonosito = $_GET['azonosito'];
    }
	
if($_GET['szla']== '1'){
   $anyag_query2 = $sql->prepare('SELECT MAX(szlaszam) FROM szamlafej');
   $anyag_query2->execute();
   $anyag_nev_result = $anyag_query2->get_result();
   $row = $anyag_nev_result->fetch_array();
   $szlaszam = $row[0];
   $szlaszam++;
   $datum = date("Y-m-d H:i:s");
   foreach ($_SESSION['kosar'] as $key => $val) {  
	  $vegosszeg = $vegosszeg + $_SESSION['kosar'][$key]['ar'];
   }
   $anyag_nev_query = $sql->query("INSERT INTO szamlafej (szlaszam, datum, vegosszeg) VALUES ($szlaszam, '$datum', $vegosszeg)");
   
   $szamla = '<h2>Számlaszám: '.$szlaszam.'</h2>';
   $szamla .= '<h3>Kelt: '.$datum.'</h3>';
   $szamla .= '<p>Tételek:</p>';
   $szamla .= '<table>';
   
   foreach ($_SESSION['kosar'] as $key => $val) {  
	  $azonosito = $_SESSION['kosar'][$key]['azonosito'];
	  $rendmenny = $_SESSION['kosar'][$key]['adag'];
	  $anyag_nev_query = $sql->query("INSERT INTO szamlatetel (szlaszam, azonosito, rendmenny) VALUES ($szlaszam, $azonosito, $rendmenny)");  
	  $szamla .= "<tr><td>".$rendmenny." adag</td><td>".$_SESSION['kosar'][$key]['nev']."</td><td>".$_SESSION['kosar'][$key]['ar']." Ft</td></tr>";
   }
   
   $szamla .= '</table>';
   $szamla .= '<h3>Végösszeg: '.$vegosszeg.' Ft</h3>';
   $szamla .= '<a href="13a.php">Vissza</a>';
   
   unset($_SESSION['kosar']);
}
	
if (isset($_GET['adag'])){
   $azonosito = $_GET['azonosito'];
   $adag = $_GET['adag'];
   $etlap_elnevezes_query = $sql->prepare('SELECT elnevezes, ar, azonosito FROM etlap WHERE azonosito = ?');
   $etlap_elnevezes_query->bind_param('i', $azonosito);
   $etlap_elnevezes_query->execute();
   $etlap_elnevezes_result = $etlap_elnevezes_query->get_result();
   $row = $etlap_elnevezes_result->fetch_assoc();
   $etlap_elnevezes = $row['elnevezes'];
   $etlap_azonosito = $row['azonosito'];
   $ar = $row['ar'];
   $_SESSION['kosar'][$azonosito]['nev'] = $etlap_elnevezes;
   $_SESSION['kosar'][$azonosito]['azonosito'] = $etlap_azonosito;
   $_SESSION['kosar'][$azonosito]['adag'] = $adag;
   $_SESSION['kosar'][$azonosito]['ar'] = $adag*$ar;
}

$etlap_query = $sql->query('SELECT e.azonosito, e.elnevezes, f.tipnev FROM etlap AS e 
					                    LEFT JOIN fajta AS f ON e.tipus = f.tipus
										ORDER BY f.tipus');
while ($etel = $etlap_query->fetch_assoc()) {
   $selected = '';
   if($etel['azonosito'] == $azonosito){
      $selected = 'selected';
   }
   $select_string .= sprintf("<option value=%s %s>%s</option>\n", $etel['azonosito'], $selected, $etel['elnevezes'].' ('.$etel['tipnev'].')');
}

?>
<html>
<head>
    <title>Schőn Péter</title>
</head>
<body>
<?php
if (!isset($szamla)){
?>
   <h1>Rendelés felvétel</h1>
    <form>
	   <label>Étel:</label>
        <select name="azonosito">
        <?php
            echo $select_string;
        ?>
        </select>
	   <label>Adag:</label>
	    <input type="text" name="adag" value="1" style="width: 30px;">
	    <input type="submit" value="Rendeléshez ad">
    </form>  
<?php
}
if (isset($_SESSION['kosar'])){
   foreach ($_SESSION['kosar'] as $key => $val) {  
	  echo $_SESSION['kosar'][$key]['adag'] . ' adag ' . $_SESSION['kosar'][$key]['nev'].' - '.$_SESSION['kosar'][$key]['ar'].' Ft<br />';
   }
   echo '<br /><a href="13a.php?torol=1">kosár kiürítése</a>';
   echo '<br /><a href="13a.php?szla=1">számla készítése</a>';
}

if (isset($szamla)){
   echo $szamla;
}
?>
</body>
</html>