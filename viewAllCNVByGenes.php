<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$gene_id_2 = $_GET['gene_id_2'];
$cnv_data_option_2 = $_GET['cnv_data_option_2'];

if (is_string($gene_id_2)) {
    $gene_arr = preg_split("/[;, \n]+/", $gene_id_2);
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }
} elseif (is_array($gene_id_2)) {
    $gene_arr = $gene_id_2;
    for ($i = 0; $i < count($gene_arr); $i++) {
        $gene_arr[$i] = trim($gene_arr[$i]);
    }
} else {
    echo "<p>Please input correct gene IDs!!!</p>";
    exit(0);
}


?>

<!-- Back button -->
<a href="/SoybeanGenVarX/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Query gene regions from database -->
<?php
$query_str = "SELECT Chromosome, Start, End, Strand, Name AS Gene_ID, Gene_Description FROM soykb.mViz_Soybean_GFF";
$query_str = $query_str . " WHERE (Name IN ('";
for ($i = 0; $i < count($gene_arr); $i++) {
    if ($i < (count($gene_arr)-1)){
        $query_str = $query_str . $gene_arr[$i] . "', '";
    } else {
        $query_str = $query_str . $gene_arr[$i];
    }
}
$query_str = $query_str . "'));";

$stmt = $PDO->prepare($query_str);
$stmt->execute();
$result = $stmt->fetchAll();

$gene_result_arr = pdoResultFilter($result);

?>

<!-- Render gene table -->
<h3>Queried genes: </h3>
<?php
if (isset($gene_result_arr) && is_array($gene_result_arr) && !empty($gene_result_arr)) {
    // Make the gene table
    echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
    echo "<table style='text-align:center; border:3px solid #000;'>";

    // Table header
    echo "<tr>";
    foreach ($gene_result_arr[0] as $key => $value) {
        echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
    }
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($gene_result_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        foreach ($gene_result_arr[$j] as $key => $value) {
            echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<br />";
} else {
    echo "<p>Genes could not be found in the database!!!</p>";
}

?>

<!-- Query frequency table from database -->
<?php
if(isset($gene_result_arr) && is_array($gene_result_arr) && !empty($gene_result_arr)) {

    $query_str = "SELECT Chromosome, Start, End, Width, Strand, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN0', 1, null)) AS CN0, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN1', 1, null)) AS CN1, ";
    if ($cnv_data_option_2 == "Consensus_Regions") {
        $query_str = $query_str . "COUNT(IF(CN = 'CN2', 1, null)) AS CN2, ";
    }
    $query_str = $query_str . "COUNT(IF(CN = 'CN3', 1, null)) AS CN3, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN4', 1, null)) AS CN4, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN5', 1, null)) AS CN5, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN6', 1, null)) AS CN6, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN7', 1, null)) AS CN7, ";
    $query_str = $query_str . "COUNT(IF(CN = 'CN8', 1, null)) AS CN8 ";
    $query_str = $query_str . "FROM ";
    if ($cnv_data_option_2 == "Individual_Hits") {
        $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
    } else if ($cnv_data_option_2 == "Consensus_Regions") {
        $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
    }
    $query_str = $query_str . "WHERE ";

    for ($i = 0; $i < count($gene_result_arr); $i++) {
        if($i < (count($gene_result_arr)-1)){
            $query_str = $query_str . "((Chromosome = '" . $gene_result_arr[$i]["Chromosome"] . "') AND (Start <= " . $gene_result_arr[$i]["Start"] . ") AND (End >= " . $gene_result_arr[$i]["End"] . ")) OR";
        } elseif ($i == (count($gene_result_arr)-1)) {
            $query_str = $query_str . "((Chromosome = '" . $gene_result_arr[$i]["Chromosome"] . "') AND (Start <= " . $gene_result_arr[$i]["Start"] . ") AND (End >= " . $gene_result_arr[$i]["End"] . ")) ";
        }
    }

    $query_str = $query_str . "GROUP BY Chromosome, Start, End, Width, Strand ";
    $query_str = $query_str . "ORDER BY Chromosome, Start, End;";

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $cnv_result_arr = pdoResultFilter($result);

}

?>

<!-- Render frequency table -->
<h3>CNV regions and accession counts in different CNs: </h3>
<?php
if(isset($cnv_result_arr) && is_array($cnv_result_arr) && !empty($cnv_result_arr)) {
    // Make the frequency table
    echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
    echo "<table style='text-align:center; border:3px solid #000;'>";
    
    // Table header
    echo "<tr>";
    foreach ($cnv_result_arr[0] as $key => $value) {
        echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
    }
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($cnv_result_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        foreach ($cnv_result_arr[$j] as $key => $value) {
            echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
        }
        echo "<td><a href=\"/SoybeanGenVarX/viewCNVAndImprovementStatus.php?chromosome_1=" . $cnv_result_arr[$j]["Chromosome"] . "&position_start_1=" . $cnv_result_arr[$j]["Start"] . "&position_end_1=" . $cnv_result_arr[$j]["End"] . "&cnv_data_option_1=" . $cnv_data_option_2 . "\" target=\"_blank\" ><button>View Details</button></a></td>";
        echo "<td><a href=\"/SoybeanGenVarX/viewCNVAndPhenotype.php?chromosome_1=" . $cnv_result_arr[$j]["Chromosome"] . "&position_start_1=" . $cnv_result_arr[$j]["Start"] . "&position_end_1=" . $cnv_result_arr[$j]["End"] . "&cnv_data_option_1=" . $cnv_data_option_2 . "\" target=\"_blank\" ><button>Connect Phenotypes</button></a></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<br />";
} else {
    echo "<p>No CNV can be mapped by the gene regions!!!</p>";
}
?>

<!-- Query CNV and Gene mapping table from database -->
<h3>Neighbouring genes in different CNV regions: </h3>
<?php
if(isset($gene_result_arr) && is_array($gene_result_arr) && !empty($gene_result_arr) && isset($cnv_result_arr) && is_array($cnv_result_arr) && !empty($cnv_result_arr)) {

    for ($i = 0; $i < count($cnv_result_arr); $i++) {
        $query_str = "SELECT CNV.Chromosome, CNV.Start AS CNV_Start, CNV.End AS CNV_End, CNV.Width AS CNV_Width, CNV.Strand AS CNV_Strand, ";
        $query_str = $query_str . "GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Name AS Gene_Name, GFF.Gene_Description ";
        $query_str = $query_str . "FROM ( ";
        $query_str = $query_str . "SELECT DISTINCT Chromosome, Start, End, Width, Strand ";
        $query_str = $query_str . "FROM ";
        if ($cnv_data_option_2 == "Individual_Hits") {
            $query_str = $query_str . "soykb.mViz_Soybean_CNVS ";
        } else if ($cnv_data_option_2 == "Consensus_Regions") {
            $query_str = $query_str . "soykb.mViz_Soybean_CNVR ";
        }
        $query_str = $query_str . "WHERE ";
        $query_str = $query_str . "(Chromosome = '" . $cnv_result_arr[$i]["Chromosome"] . "') AND (Start = " . $cnv_result_arr[$i]["Start"] . ") AND (End = " . $cnv_result_arr[$i]["End"] . ") ";
        $query_str = $query_str . ") AS CNV ";
        $query_str = $query_str . "LEFT JOIN soykb.mViz_Soybean_GFF AS GFF ON ";
        $query_str = $query_str . "(CNV.Chromosome = GFF.Chromosome AND CNV.Start <= GFF.Start AND CNV.End >= GFF.End) ";
        $query_str = $query_str . "ORDER BY CNV.Chromosome, CNV.Start, GFF.Start, GFF.End;";

        $stmt = $PDO->prepare($query_str);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $cnv_gene_mapping_result_arr = pdoResultFilter($result);

        // Render CNV and gene mapping table or print not found message
        if(isset($cnv_gene_mapping_result_arr) && is_array($cnv_gene_mapping_result_arr) && !empty($cnv_gene_mapping_result_arr)) {
            // Make the cnv and gene mapping table
            echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
            echo "<table style='text-align:center; border:3px solid #000;'>";
            
            // Table header
            echo "<tr>";
            foreach ($cnv_gene_mapping_result_arr[0] as $key => $value) {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            }
            echo "</tr>";
        
            // Table body
            for ($j = 0; $j < count($cnv_gene_mapping_result_arr); $j++) {
                $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");
        
                echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
                foreach ($cnv_gene_mapping_result_arr[$j] as $key => $value) {
                    echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
                }
                echo "</tr>";
            }
        
            echo "</table>";
            echo "</div>";
        
            echo "<br />";
        } else {
            echo "<p>CNV region (" . $cnv_result_arr[$i]["Chromosome"] . ":" . $cnv_result_arr[$i]["Start"] . "-" . $cnv_result_arr[$i]["End"] . ")does not have any gene!!!</p>";
        }
    }
} else { 
    echo "<p>No Gene and CNV mapping could be found!!!</p>";
}
?>

<?php include '../footer.php'; ?>
