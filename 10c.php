<?php
    $sql = mysqli_connect('localhost', 'root', '');
    $sql->select_db('etterem');
    $sql->set_charset("utf8");
    $akod = false;
    $azonosito = false;
    if(isset($_GET['akod'])){
        $akod = $_GET['akod'];
    }
    if(isset($_GET['beszerzes'])){
        $beszerzes = $_GET['beszerzes'];
    }
?>
<html>
<head>
    <title>Nemes Gergő - VKWQ1I</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
   <h1>Anyagbeszerzés</h1>
    <form>
        <label>Áru:</label><br />
        <?php
            $anyag_query = $sql->query('SELECT akod, nev, mertegys FROM anyag');
            while ($anyag = $anyag_query->fetch_assoc()) {
                $checked = '';
                if($anyag['akod'] == $akod){
                    $checked = 'checked';
                }
                printf("<input type='radio' name='akod' value=%s %s>%s (%s)</option>\n<br/>", $anyag['akod'], $checked, $anyag['nev'], $anyag['mertegys']);
            }
        ?>
		<label>Mennyiség:</label>
		 <input type="text" name="beszerzes" value=""><br />
        <input type="submit" value="Rögzít">
        <?php
            if($akod){
                $anyag_nev_query = $sql->prepare('SELECT nev, keszlet, mertegys, egysar FROM anyag WHERE akod = ?');
                $anyag_nev_query->bind_param('i', $akod);
                $anyag_nev_query->execute();
                $anyag_nev_result = $anyag_nev_query->get_result();
				$row = $anyag_nev_result->fetch_assoc();
                $keszlet = $row[keszlet];
			    $mertegys = $row[mertegys];
			    $nev = $row[nev];
			    $egysar = $row[egysar];
			    $uj_keszlet = ($keszlet + $beszerzes);
				$bear = $beszerzes * $egysar;
				
                echo '<h2>'.$nev.' beszerzés</h2>';
			   echo 'Eredeti készlet: '. $keszlet. ' '.$mertegys.'<br />';
			   echo 'Beszerzés: '. $beszerzes. ' '.$mertegys.' ('.$bear.' Ft)<br />';
			   echo 'Új készlet: '. $uj_keszlet. ' '.$mertegys.'<br />';
			   
               $anyag_nev_query = $sql->query('UPDATE anyag SET keszlet = '.$uj_keszlet.' WHERE akod = '.$akod);
                
			   $datum = date("Y-m-d"); 
				
			   $anyag_nev_query = $sql->query('INSERT INTO beszerzes (akod, datum, bear, menny) VALUES ('.$akod.', '.$datum.', '.$bear.', '.$beszerzes);
            }
            ?>
           
</body>
</html>