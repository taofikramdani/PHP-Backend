<?php

include('koneksi.php');

if( !$koneksi){
    return;
}

// nilai-nilai data saat suhu maksimum dan humid maksimum
$query0 = "
    SELECT id, suhu, humid, lux, DATE_FORMAT(ts, '%Y-%m-%d %H:%i:%s') AS timestamp 
    FROM tb_cuaca 
    WHERE suhu = (SELECT MAX(suhu) FROM tb_cuaca) 
    AND humid = (SELECT MAX(humid) FROM tb_cuaca)
";
$result0 = mysqli_query($koneksi, $query0);

$arrSuhuMaxHumidMax = array();
$monthYearMaxArray = array();  
if( mysqli_num_rows($result0) > 0){
    while($rows = mysqli_fetch_assoc($result0)){
        $arr = array(
            'idx' => $rows['id'],
            'suhun' => $rows['suhu'],
            'humid' => $rows['humid'],
            'kecerahan' => $rows['lux'],
            'timestamp' => $rows['timestamp'] 
        );
        array_push($arrSuhuMaxHumidMax, $arr);

        $monthYearMax = array('month_year' => date('n-Y', strtotime($rows['timestamp']))); 
        array_push($monthYearMaxArray, $monthYearMax); 
    }
}


$query2 = "SELECT id, suhu, humid, lux, DATE_FORMAT(ts, '%Y-%m-%d %H:%i:%s') AS timestamp FROM tb_cuaca WHERE suhu = (SELECT MIN(suhu) FROM tb_cuaca)";
$result2 = mysqli_query($koneksi, $query2);

$arrSuhuMin = array();
if( mysqli_num_rows($result2) > 0){
    while($rows = mysqli_fetch_assoc($result2)){
        $arr = array(
            'idx' => $rows['id'],
            'suhun' => $rows['suhu'],
            'humid' => $rows['humid'],
            'kecerahan' => $rows['lux'],
            'timestamp' => $rows['timestamp'] 
        );
        array_push($arrSuhuMin, $arr);
    }
}

// nilai min, max, rata-rata
$query1 = "SELECT MAX(suhu) AS maks_suhu, MIN(suhu) AS min_suhu, Round(AVG(suhu),2) AS rata_suhu FROM tb_cuaca";
$result = mysqli_query($koneksi, $query1);

if( mysqli_num_rows($result) > 0){
    while($rows = mysqli_fetch_assoc($result)){
        $data = array(
            'suhumax' => $rows['maks_suhu'],
            'suhumin' => $rows['min_suhu'],
            'suhurata' => $rows['rata_suhu'],
            'nilai_suhu_max_humid_max' => $arrSuhuMaxHumidMax, 
            'month_year_max' => $monthYearMaxArray 
        );
    }

    echo json_encode($data, JSON_PRETTY_PRINT);
}
else{
    echo 'tidak ada data';
}

?>
