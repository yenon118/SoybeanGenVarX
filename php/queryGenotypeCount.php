<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$start = $_GET['Start'];
$end = $_GET['End'];

$query_str = "
    SELECT * 
    FROM soykb.mViz_Soy1066_" . $chromosome . "_genotype_count 
    WHERE (Chromosome = '" . $chromosome . "')
    AND (Position BETWEEN " . $start . " AND " . $end . ") 
    ORDER BY Chromosome, Position, Count DESC;
";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
