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
   <h1>Étlap (Rendelés ellenőrzés)</h1>
    <form>
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
        </select><input type="submit" value="Keres">
        <?php
            if(isset($_GET['azonosito'])){
                $azonosito = $_GET['azonosito'];
                
                $etlap_elnevezes_query = $sql->prepare('SELECT elnevezes FROM etlap WHERE azonosito = ?');
                $etlap_elnevezes_query->bind_param('i', $azonosito);
                $etlap_elnevezes_query->execute();
                $etlap_elnevezes_result = $etlap_elnevezes_query->get_result();
                $row = $etlap_elnevezes_result->fetch_assoc();
				$etlap_elnevezes = $row['elnevezes'];
                
                printf("<h1>%s receptje</h1>\n", $etlap_elnevezes);
            ?>
            <table>
                <tr>
                    <td>Anyag</td>
                    <td>Mennyiség</td>
                </tr>
                <?php
                    $anyagok_query = $sql->prepare('SELECT a.nev, r.szuksmenny, a.mertegys, a.keszlet 
												    FROM recept AS r
													LEFT JOIN anyag AS a
													ON r.akod = a.akod
													WHERE r.azonosito = ?');
                    $anyagok_query->bind_param('i', $azonosito);
                    $anyagok_query->execute();
                    $anyagok_result = $anyagok_query->get_result();
                    
                    while($anyag = $anyagok_result->fetch_assoc()){
					   
					   
					   //megnézzük, hogy van-e készleten az adott összetevő
					   if ($anyag['keszlet'] < $anyag['szuksmenny']){
						  $hiany[] = $anyag['nev'];
					   }
					   
                        printf("<tr><td>%s</td><td>%s %s</td></tr>\n", $anyag['nev'], $anyag['szuksmenny'], $anyag['mertegys']);
                    }
                } ?>
            </table>
        </select>
    </form>
<?php
if ($hiany){
   echo '<h2>Az étel nem rendelhető!</h2>';
   echo 'Hiányzó összetevők: <br />';
   foreach ($hiany as $key=>$value){
	  echo $value.'<br/>';
   }
} else{
   if (isset($_GET['azonosito'])){
   echo '<h2>Az étel rendelhető!</h2>';
   }
}
?>
</body>
</html>