<?php
    $sql = mysqli_connect('localhost', 'root', '');
    $sql->select_db('etterem');
    $sql->set_charset("utf8");
    $akod = false;
    $azonosito = false;
    if(isset($_GET['tipus'])){
        $tipus = $_GET['tipus'];
    }
?>
<html>
<head>
    <title>Schőn Péter</title>
</head>
<body>
   <h1>Étlap</h1>
    <form>
        <select name="tipus">
        <?php
            $etlap_query = $sql->query('SELECT e.azonosito, e.elnevezes, f.tipnev, f.tipus FROM etlap AS e 
					                    LEFT JOIN fajta AS f ON e.tipus = f.tipus
										GROUP BY f.tipus ORDER BY f.tipus');
            while ($etel = $etlap_query->fetch_assoc()) {
                $selected = '';
                if($etel['tipus'] == $tipus){
                    $selected = 'selected';
                }
                printf("<option value=%s %s>%s</option>\n", $etel['tipus'], $selected, $etel['tipnev']);
            }
        ?>
        </select><input type="submit" value="Keres">
        <?php
            if(isset($_GET['tipus'])){
                $tipus = $_GET['tipus'];
                
                $etlap_elnevezes_query = $sql->prepare('SELECT tipnev FROM fajta WHERE tipus = ?');
                $etlap_elnevezes_query->bind_param('i', $tipus);
                $etlap_elnevezes_query->execute();
                $etlap_elnevezes_result = $etlap_elnevezes_query->get_result();
                $row = $etlap_elnevezes_result->fetch_assoc();
				$etlap_elnevezes = $row['tipnev'];
                
                printf("<h1>%s ételek</h1>\n", $etlap_elnevezes);
            ?>
            <table>
                <tr>
                    <td>Megnevezés</td>
                </tr>
                <?php
                    $anyagok_query = $sql->prepare('SELECT elnevezes FROM etlap WHERE tipus = ?');
                    $anyagok_query->bind_param('i', $tipus);
                    $anyagok_query->execute();
                    $anyagok_result = $anyagok_query->get_result();
                    
                    while($anyag = $anyagok_result->fetch_assoc()){
                        printf("<tr><td>%s</td></tr>\n", $anyag['elnevezes']);
                    }
                } ?>
            </table>
        </select>
    </form>
</body>
</html>