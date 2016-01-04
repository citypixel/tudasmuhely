<?php
    $sql = mysqli_connect('localhost', 'root', 'root');
    $sql->select_db('etterem');
    
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
</head>
<body>
    <form>
        
        <?php
            $anyag_query = $sql->query('SELECT akod, nev FROM anyag');
            while ($anyag = $anyag_query->fetch_assoc()) {
                $checked = '';
                if($anyag['akod'] == $akod){
                    $checked = 'checked';
                }
                printf("<input type='radio' name='akod' value=%s %s>%s</option>\n<br/>", $anyag['akod'], $checked, $anyag['nev']);
            }
        ?>
        <input type="submit" value="Keres">
        <?php
            if($akod){
                $anyag_nev_query = $sql->prepare('SELECT nev FROM anyag WHERE akod = ?');
                $anyag_nev_query->bind_param('i', $akod);
                $anyag_nev_query->execute();
                $anyag_nev_result = $anyag_nev_query->get_result();
                $anyag_nev = $anyag_nev_result->fetch_assoc()['nev'];
                printf("<h1>Ételek amikben %s van:</h1>\n", $anyag_nev);

                $etel_query = $sql->prepare('SELECT etlap.azonosito, etlap.elnevezes
                                              FROM anyag
                                              NATURAL JOIN recept
                                              NATURAL JOIN etlap
                                              WHERE anyag.akod = ?');
                
                $etel_query->bind_param('i', $akod);
                $etel_query->execute();
                $etel_result = $etel_query->get_result();
                while($etel = $etel_result->fetch_assoc()){
                    printf("<a href='./06c.php?akod=%s&azonosito=%s'>%s</a><br>\n", $akod, $etel['azonosito'], $etel['elnevezes']);
                }
            }
            if(isset($_GET['azonosito'])){
                $azonosito = $_GET['azonosito'];
                
                $etlap_elnevezes_query = $sql->prepare('SELECT elnevezes FROM etlap WHERE azonosito = ?');
                $etlap_elnevezes_query->bind_param('i', $azonosito);
                $etlap_elnevezes_query->execute();
                $etlap_elnevezes_result = $etlap_elnevezes_query->get_result();
                $etlap_elnevezes = $etlap_elnevezes_result->fetch_assoc()['elnevezes'];
                
                printf("<h1>%s receptje</h1>\n", $etlap_elnevezes);
            ?>
            <table>
                <tr>
                    <td>Anyag</td>
                    <td>Mennyiség</td>
                </tr>
                <?php
                    $anyagok_query = $sql->prepare('SELECT nev, egysar, mertegys
                                                    FROM anyag
                                                    NATURAL JOIN recept
                                                    WHERE recept.azonosito = ?');
                    $anyagok_query->bind_param('i', $azonosito);
                    $anyagok_query->execute();
                    $anyagok_result = $anyagok_query->get_result();
                    
                    while($anyag = $anyagok_result->fetch_assoc()){
                        printf("<tr><td>%s</td><td>%s %s</td></tr>\n", $anyag['nev'], $anyag['egysar'], $anyag['mertegys']);
                    } ?>
                 </table>
        </select>
    </form>
     <hr/>
        <form>
        <input type="submit" value="Új alapanyag választás" />
        </form>
         <?php } ?>
           
</body>
</html>