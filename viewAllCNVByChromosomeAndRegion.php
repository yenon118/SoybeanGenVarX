<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome_2 = $_GET['chromosome_2'];
$position_start_2 = $_GET['position_start_2'];
$position_end_2 = $_GET['position_end_2'];
$cnv_data_option_2 = $_GET['cnv_data_option_2'];

$chromosome_2 = trim($chromosome_2);
$position_start_2 = intval(trim($position_start_2))-1;
$position_end_2 = intval(trim($position_end_2))+1;
$cnv_data_option_2 = trim($cnv_data_option_2);
?>

<!-- Back button -->
<a href="/SoybeanGenVarX/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query cnv accession count data from database -->
<?php

$query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN0', 1, null)) AS CN0, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN1', 1, null)) AS CN1, ";
if ($cnv_data_option_2 == "Consensus_Regions") {
    $query_str = $query_str . "COUNT(IF(CNV.CN = 'CN2', 1, null)) AS CN2, ";
}
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN3', 1, null)) AS CN3, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN4', 1, null)) AS CN4, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN5', 1, null)) AS CN5, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN6', 1, null)) AS CN6, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN7', 1, null)) AS CN7, ";
$query_str = $query_str . "COUNT(IF(CNV.CN = 'CN8', 1, null)) AS CN8 ";
$query_str = $query_str . "FROM ";
if ($cnv_data_option_2 == "Individual_Hits") {
    $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
} else if ($cnv_data_option_2 == "Consensus_Regions") {
    $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
}
$query_str = $query_str . "AS CNV ";
$query_str = $query_str . "WHERE (CNV.Chromosome = '" . $chromosome_2 . "') ";
$query_str = $query_str . "AND (CNV.Start BETWEEN " . $position_start_2 . " AND " . $position_end_2 . ") ";
$query_str = $query_str . "AND (CNV.End BETWEEN " . $position_start_2 . " AND " . $position_end_2 . ") ";
$query_str = $query_str . "GROUP BY CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand ";
$query_str = $query_str . "ORDER BY CNV.Chromosome, CNV.Start, CNV.End; ";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $cnv_accession_count_result_arr = pdoResultFilter($result);
}

?>


<!-- Render the table -->
<h3>Queried CNV region: </h3>
<?php

    if (isset($cnv_accession_count_result_arr) && is_array($cnv_accession_count_result_arr) && !empty($cnv_accession_count_result_arr) && count($cnv_accession_count_result_arr) > 0) {
        echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
        echo "<table style='text-align:center;'>";

        // Table header
        echo "<tr>";
        foreach ($cnv_accession_count_result_arr[0] as $key => $value) {
            echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
        }
        echo "</tr>";

        // Table body
        for ($i = 0; $i < count($cnv_accession_count_result_arr); $i++) {
            $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            foreach ($cnv_accession_count_result_arr[$i] as $key => $value) {
                echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
            }
            echo "<td><a href=\"/SoybeanGenVarX/viewCNVAndImprovementStatus.php?chromosome_1=" . $cnv_accession_count_result_arr[$i]["Chromosome"] . "&position_start_1=" . $cnv_accession_count_result_arr[$i]["Start"] . "&position_end_1=" . $cnv_accession_count_result_arr[$i]["End"] . "&cnv_data_option_1=" . $cnv_data_option_2 . "\" target=\"_blank\" ><button>View Details</button></a></td>";
            echo "<td><a href=\"/SoybeanGenVarX/viewCNVAndPhenotype.php?chromosome_1=" . $cnv_accession_count_result_arr[$i]["Chromosome"] . "&position_start_1=" . $cnv_accession_count_result_arr[$i]["Start"] . "&position_end_1=" . $cnv_accession_count_result_arr[$i]["End"] . "&cnv_data_option_1=" . $cnv_data_option_2 . "\" target=\"_blank\" ><button>Connect Phenotypes</button></a></td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<br /><br />";
    } else {
        echo "The queried CNV region cannot be found!!!";
    }

?>


<!-- Query data from database and render the data -->
<h3>Accessions and CNs within the queried CNV region: </h3>
<?php
if (isset($cnv_accession_count_result_arr) && is_array($cnv_accession_count_result_arr) && !empty($cnv_accession_count_result_arr) && count($cnv_accession_count_result_arr) > 0) {
    for ($i = 0; $i < count($cnv_accession_count_result_arr); $i++) {

        // Check GRIN accession mapping
        $query_str = "SELECT CNV.Chromosome, CNV.Start, CNV.End, CNV.Width, CNV.Strand, AM.SoyKB_Accession AS Accession, CNV.CN ";
        $query_str = $query_str . "FROM ";
        if ($cnv_data_option_2 == "Individual_Hits") {
            $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
        } else if ($cnv_data_option_2 == "Consensus_Regions") {
            $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
        }
        $query_str = $query_str . "AS CNV ";
        $query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_Accession_Mapping AS AM ";
        $query_str = $query_str . "ON BINARY CNV.Accession = AM.Accession ";
        $query_str = $query_str . "WHERE (CNV.Chromosome = '" . $cnv_accession_count_result_arr[$i]["Chromosome"] . "') ";
        $query_str = $query_str . "AND (CNV.Start BETWEEN " . $cnv_accession_count_result_arr[$i]["Start"] . " AND " . $cnv_accession_count_result_arr[$i]["End"] . ") ";
        $query_str = $query_str . "AND (CNV.End BETWEEN " . $cnv_accession_count_result_arr[$i]["Start"] . " AND " . $cnv_accession_count_result_arr[$i]["End"] . ") ";
        $query_str = $query_str . "ORDER BY CNV.CN, AM.SoyKB_Accession; ";

        $stmt = $PDO->prepare($query_str);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $result_arr = pdoResultFilter($result);

        // Render the data
        if (isset($result_arr) && is_array($result_arr) && !empty($result_arr) && count($result_arr) > 0) {
            echo "<div style='width:auto; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px; display:inline-block;'>";
            echo "<table style='text-align:center;'>";

            // Table header
            echo "<tr>";
            foreach ($result_arr[0] as $key => $value) {
                echo "<th style=\"border:1px solid black;\">" . strval($key) . "</th>";
            }
            echo "</tr>";

            // Table body
            for ($j = 0; $j < count($result_arr); $j++) {
                $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

                echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
                foreach ($result_arr[$j] as $key => $value) {
                    echo "<td style=\"border:1px solid black;min-width:120px;\">" . $value . "</td>";
                }
                echo "</tr>";
            }

            echo "</table>";
            echo "</div>";

            echo "<br /><br />";
        } else {
            echo "No accession is within CNV region (" . $cnv_accession_count_result_arr[$i]["Chromosome"] . ":" . $cnv_accession_count_result_arr[$i]["Start"] . "-" . $cnv_accession_count_result_arr[$i]["End"] . ")!!!";
        }
    }
} else {
    echo "No accession found since the queried CNV region cannot be found!!!";
}

?>


<?php include '../footer.php'; ?>
