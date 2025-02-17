<?php

include '../../config.php';
include 'pdoResultFilter.php';

$chromosome = $_GET['Chromosome'];
$start = $_GET['Start'];
$end = $_GET['End'];


$chromosome = clean_malicious_input($chromosome);
$chromosome = preg_replace('/\s+/', '', $chromosome);

$start = clean_malicious_input($start);
$start = preg_replace('/\s+/', '', $start);
$start = preg_replace("/[^0-9.]/", "", $start);

$end = clean_malicious_input($end);
$end = preg_replace('/\s+/', '', $end);
$end = preg_replace("/[^0-9.]/", "", $end);


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
