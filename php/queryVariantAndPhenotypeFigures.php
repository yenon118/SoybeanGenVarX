<?php

include '../../config.php';
include 'pdoResultFilter.php';


$chromosome = $_GET['Chromosome'];
$position = $_GET['Position'];
$genotype = $_GET['Genotype'];
$phenotype = $_GET['Phenotype'];


$chromosome = clean_malicious_input($chromosome);
$chromosome = preg_replace('/\s+/', '', $chromosome);

$position = clean_malicious_input($position);
$position = preg_replace('/\s+/', '', $position);
$position = preg_replace("/[^0-9.]/", "", $position);

$genotype = clean_malicious_input($genotype);

$phenotype = clean_malicious_input($phenotype);


$dataset = "Soy1066";
$db = "soykb";
$genotype_table = "mViz_" . $dataset . "_" . $chromosome . "_genotype_data";
$accession_mapping_table = "mViz_Soybean_Accession_Mapping";
$phenotype_table = "mViz_Soybean_Phenotype_Data";


if (isset($genotype)) {
    if (is_string($genotype)) {
        $genotype = trim($genotype);
        $temp_genotype_array = preg_split("/[;, \n]+/", $genotype);
        $genotype_array = array();
        for ($i = 0; $i < count($temp_genotype_array); $i++) {
            if (!empty(trim($temp_genotype_array[$i]))) {
                array_push($genotype_array, trim($temp_genotype_array[$i]));
            }
        }
    } elseif (is_array($genotype)) {
        $temp_genotype_array = $genotype;
        $genotype_array = array();
        for ($i = 0; $i < count($temp_genotype_array); $i++) {
            if (!empty(trim($temp_genotype_array[$i]))) {
                array_push($genotype_array, trim($temp_genotype_array[$i]));
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
$query_str = "SELECT GENO.Chromosome, GENO.Position, GENO.Accession, ";
$query_str = $query_str . "AM.GRIN_Accession, AM.Improvement_Status, AM.Classification, ";
$query_str = $query_str . "GENO.Genotype, GENO.Category, GENO.Imputation ";

if (isset($phenotype_array)) {
    for ($i = 0; $i < count($phenotype_array); $i++) {
        $query_str = $query_str . ", PH." . $phenotype_array[$i] . " ";
    }
}

$query_str = $query_str . "FROM ( ";
$query_str = $query_str . "    SELECT G.Chromosome, G.Position, G.Accession, G.Genotype, G.Category, G.Imputation ";
$query_str = $query_str . "    FROM " . $db . "." . $genotype_table . " AS G ";
$query_str = $query_str . "    WHERE (G.Chromosome = '" . $chromosome . "') ";
$query_str = $query_str . "    AND (G.Position = " . $position . ") ";

if (isset($genotype_array)) {
    if (count($genotype_array) > 0) {
        $query_str = $query_str . "    AND (G.Genotype IN ('";
        for ($i = 0; $i < count($genotype_array); $i++) {
            if ($i < (count($genotype_array) - 1)) {
                $query_str = $query_str . trim($genotype_array[$i]) . "', '";
            } elseif ($i == (count($genotype_array) - 1)) {
                $query_str = $query_str . trim($genotype_array[$i]);
            }
        }
        $query_str = $query_str . "')) ";
    }
}

$query_str = $query_str . ") AS GENO ";
$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
$query_str = $query_str . "ON CAST(GENO.Accession AS BINARY) = CAST(AM.SoyKB_Accession AS BINARY) ";

if (isset($phenotype_array)) {
    $query_str = $query_str . "LEFT JOIN " . $db . "." . $phenotype_table . " AS PH ";
    $query_str = $query_str . "ON CAST(AM.GRIN_Accession AS BINARY) = CAST(PH.ACNO AS BINARY) ";
}

$query_str = $query_str . "ORDER BY GENO.Chromosome, GENO.Position, GENO.Genotype; ";


$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$result_arr = pdoResultFilter($result);

echo json_encode(array("data" => $result_arr), JSON_INVALID_UTF8_IGNORE);

?>
