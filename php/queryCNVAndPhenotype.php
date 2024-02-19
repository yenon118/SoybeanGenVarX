<?php

include '../../config.php';
include 'pdoResultFilter.php';


$chromosome = $_GET['Chromosome'];
$position_start = $_GET['Start'];
$position_end = $_GET['End'];
$data_option = $_GET['Data_Option'];
$cn = $_GET['CN'];
$phenotype = $_GET['Phenotype'];


$db = "soykb";
$accession_mapping_table_name = "mViz_Soybean_Accession_Mapping";
$phenotype_table_name = "mViz_Soybean_Phenotype_Data";

if (isset($data_option)) {
    if ($data_option == "Individual_Hits") {
        $cnv_table_name = "mViz_Soybean_CNVS ";
    } else if ($data_option == "Consensus_Regions") {
        $cnv_table_name = "mViz_Soybean_CNVR ";
    }
}


if (isset($cn)) {
    if (is_string($cn)) {
        $temp_cn_array = preg_split("/[;, \n]+/", $cn);
        $cn_array = array();
        for ($i = 0; $i < count($temp_cn_array); $i++) {
            if (!empty(trim($temp_cn_array[$i]))) {
                array_push($cn_array, trim($temp_cn_array[$i]));
            }
        }
    } elseif (is_array($cn)) {
        $temp_cn_array = $cn;
        $cn_array = array();
        for ($i = 0; $i < count($temp_cn_array); $i++) {
            if (!empty(trim($temp_cn_array[$i]))) {
                array_push($cn_array, trim($temp_cn_array[$i]));
            }
        }
    }
}


if (isset($phenotype)) {
    if (is_string($phenotype)) {
        $phenotype = trim($phenotype);
        $temp_phenotype_array = preg_split("/[;, \n]+/", $phenotype);
        $phenotype_array = array();
        for ($i = 0; $i < count($temp_phenotype_array); $i++) {
            if (!empty(trim($temp_phenotype_array[$i]))) {
                array_push($phenotype_array, trim($temp_phenotype_array[$i]));
            }
        }
    } elseif (is_array($phenotype)) {
        $temp_phenotype_array = $phenotype;
        $phenotype_array = array();
        for ($i = 0; $i < count($temp_phenotype_array); $i++) {
            if (!empty(trim($temp_phenotype_array[$i]))) {
                array_push($phenotype_array, trim($temp_phenotype_array[$i]));
            }
        }
    }
}


// Construct query string
$query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, ";
$query_str = $query_str . "AM.SoyKB_Accession AS Accession, AM.GRIN_Accession, AM.Improvement_Status, AM.Classification, ";
$query_str = $query_str . "CNV.CN, CNV.Status ";

if (isset($phenotype_array)) {
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $query_str = $query_str . ", PH." . $phenotype_array[$i] . " ";
    }
}

$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT C.Chromosome, C.Start, C.End, C.Width, C.Strand, C.Accession, C.CN, ";
$query_str = $query_str . "    CASE C.CN ";
$query_str = $query_str . "    WHEN 'CN0' THEN 'Loss' ";
$query_str = $query_str . "    WHEN 'CN1' THEN 'Loss' ";
$query_str = $query_str . "    WHEN 'CN3' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN4' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN5' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN6' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN7' THEN 'Gain' ";
$query_str = $query_str . "    WHEN 'CN8' THEN 'Gain' ";
$query_str = $query_str . "    ELSE 'Normal' ";
$query_str = $query_str . "    END as Status ";
$query_str = $query_str . "    FROM " . $db . "." . $cnv_table_name . " AS C ";
$query_str = $query_str . "    WHERE (C.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (C.Start BETWEEN " . $position_start . " AND " . $position_end . ") ";
$query_str = $query_str . "    AND (C.End BETWEEN " . $position_start . " AND " . $position_end . ") ";

if (isset($cn_array)) {
    if (count($cn_array) > 0) {
        $query_str = $query_str . "    AND (C.CN IN ('";
        for ($i = 0; $i < count($cn_array); $i++) {
            if($i < (count($cn_array)-1)){
                $query_str = $query_str . trim($cn_array[$i]) . "', '";
            } elseif ($i == (count($cn_array)-1)) {
                $query_str = $query_str . trim($cn_array[$i]);
            }
        }
        $query_str = $query_str . "')) ";
    }
}

$query_str = $query_str . ") AS CNV ";
$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table_name . " AS AM ";
$query_str = $query_str . "ON CAST(CNV.Accession AS BINARY) = CAST(AM.Accession AS BINARY) ";

if (isset($phenotype_array)) {
    $query_str = $query_str . "LEFT JOIN " . $db . "." . $phenotype_table_name . " AS PH ";
    $query_str = $query_str . "ON CAST(AM.GRIN_Accession AS BINARY) = CAST(PH.ACNO AS BINARY) ";
}

$query_str = $query_str . "ORDER BY CNV.CN, CNV.Chromosome, CNV.Start, CNV.End, AM.SoyKB_Accession; ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);
?>