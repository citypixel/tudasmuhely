<?php

    $sql = mysqli_connect('localhost', 'root', '');
    $sql->select_db('etterem');
    $sql->set_charset("utf8");
    $akod = false;
    $azonosito = false;
    if(isset($_GET['akod'])){
        $akod = $_GET['akod'];
    }
    if(isset($_GET['azonosito'])){
        $azonosito = $_GET['azonosito'];
    }
?>
<html>
<head>
    <title>Nemes Gergő - VKWQ1I</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
   <h1>Étlap</h1>
    <form>
        
        <?php
            $anyag_query = $sql->query('SELECT e.azonosito, e.elnevezes, f.tipnev FROM etlap AS e 
					                    LEFT JOIN fajta AS f ON e.tipus = f.tipus
										ORDER BY f.tipus');
            while ($anyag = $anyag_query->fetch_assoc()) {
                $checked = '';
                if($anyag['azonosito'] == $azonosito){
                    $checked = 'checked';
                }
                printf("<input type='radio' name='azonosito' value=%s %s>%s</option>\n<br/>", $anyag['azonosito'], $checked, $anyag['elnevezes'] .' ('.$anyag['tipnev'].')');
            }
        ?>
        <input type="submit" value="Keres">
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
                    $anyagok_query = $sql->prepare('SELECT nev, szuksmenny, mertegys
                                                    FROM anyag
                                                    NATURAL JOIN recept
                                                    WHERE recept.azonosito = ?');
                    $anyagok_query->bind_param('i', $azonosito);
                    $anyagok_query->execute();
                    $anyagok_result = $anyagok_query->get_result();
                    
                    while($anyag = $anyagok_result->fetch_assoc()){
                        printf("<tr><td>%s</td><td>%s %s</td></tr>\n", $anyag['nev'], $anyag['szuksmenny'], $anyag['mertegys']);
                    } ?>
                 </table>
        </select>
    </form>
         <?php } ?>
           
</body>
</html>