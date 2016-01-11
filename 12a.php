<?php
session_start();
if (isset($_GET['torol'] )){
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
	
if (isset($_GET['adag'])){
   $azonosito = $_GET['azonosito'];
   $adag = $_GET['adag'];
   $etlap_elnevezes_query = $sql->prepare('SELECT elnevezes FROM etlap WHERE azonosito = ?');
   $etlap_elnevezes_query->bind_param('i', $azonosito);
   $etlap_elnevezes_query->execute();
   $etlap_elnevezes_result = $etlap_elnevezes_query->get_result();
   $row = $etlap_elnevezes_result->fetch_assoc();
   $etlap_elnevezes = $row['elnevezes'];
   $_SESSION['kosar'][$azonosito]['nev'] = $etlap_elnevezes;
   $_SESSION['kosar'][$azonosito]['adag'] = $adag;
}

?>
<html>
<head>
    <title>Schőn Péter</title>
</head>
<body>
   <h1>Rendelés felvétel</h1>
    <form>
	   <label>Étel:</label>
        <select name="azonosito">
        <?php
            $etlap_query = $sql->query('SELECT e.azonosito, e.elnevezes, f.tipnev FROM etlap AS e 
					                    LEFT JOIN fajta AS f ON e.tipus = f.tipus
										ORDER BY f.tipus');
            while ($etel = $etlap_query->fetch_assoc()) {
                $selected = '';
                if($etel['azonosito'] == $azonosito){
                    $selected = 'selected';
                }
                printf("<option value=%s %s>%s</option>\n", $etel['azonosito'], $selected, $etel['elnevezes'].' ('.$etel['tipnev'].')');
            }
        ?>
        </select>
	   <label>Adag:</label>
	    <input type="text" name="adag" value="1" style="width: 30px;">
	    <input type="submit" value="Rendeléshez ad">
    </form>
   
<?php
if (isset($_SESSION['kosar'])){
   foreach ($_SESSION['kosar'] as $key => $val) {  
	  echo $_SESSION['kosar'][$key]['adag'] . ' adag ' . $_SESSION['kosar'][$key]['nev'].'<br />';
   }
   echo '<br /><a href="12a.php?torol=1">kosár kiürítése</a>';
}
?>
</body>
</html>