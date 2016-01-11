<?php
    $sql = mysqli_connect('localhost', 'root', '');
    $sql->select_db('etterem');
    $sql->set_charset("utf8");
    $akod = false;
    if(isset($_GET['akod'])){
        $akod = $_GET['akod'];
    }
	if(isset($_GET['beszerzes'])){
        $beszerzes = $_GET['beszerzes'];
    }

?>
<html>
<head>
    <title>Schőn Péter</title>
</head>
<body>
    <form>
	   <h1>Anyagbeszerzés</h1>
	   <label>Áru:</label>
        <select name="akod">
        <?php
            $anyag_query = $sql->query('SELECT akod, nev, mertegys FROM anyag');
            while ($anyag = $anyag_query->fetch_assoc()) {
                $selected = '';
                if($anyag['akod'] == $akod){
                    $selected = 'selected';
                }
                printf("<option value=%s %s>%s (%s)</option>\n", $anyag['akod'], $selected, $anyag['nev'], $anyag['mertegys']);
            }
        ?>
        </select><br />
	   <label>Mennyiség:</label>
	   <input type="text" name="beszerzes" value=""><br />
	   <input type="submit" value="Rögzít"><br /><br />
<?php
            if($akod){
			   //mennyi van most készleten
			   $anyag_query2 = $sql->prepare('SELECT nev, keszlet, mertegys, egysar FROM anyag WHERE akod = ?');
			   $anyag_query2->bind_param('i', $akod);
			   $anyag_query2->execute();
			   $anyag_nev_result = $anyag_query2->get_result();
			   $row = $anyag_nev_result->fetch_assoc();
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
			   
               $anyag_nev_query = $sql->query('UPDATE anyag SET keszlet = '.$uj_keszlet.' WHERE akod = '.$akod);
                
			   $datum = date("Y-m-d"); 
				
			   $anyag_nev_query = $sql->query("INSERT INTO beszerzes (akod, datum, bear, menny) VALUES ($akod, '$datum', $bear, $beszerzes)");
			   echo $sql->error;
                
            }
?>
        </select>
    </form>
</body>
</html>