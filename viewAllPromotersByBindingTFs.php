<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$binding_tf_1 = $_GET['binding_tf_1'];
$chromosome_1 = $_GET['chromosome_1'];
$upstream_length_1 = $_GET['upstream_length_1'];

if (is_string($binding_tf_1)) {
    $temp_binding_tf_arr = preg_split("/[;, \n]+/", $binding_tf_1);
    $binding_tf_arr = array();
    for ($i = 0; $i < count($temp_binding_tf_arr); $i++) {
        if (!empty(trim($temp_binding_tf_arr[$i]))) {
            array_push($binding_tf_arr, trim($temp_binding_tf_arr[$i]));
        }
    }
} elseif (is_array($binding_tf_1)) {
    $temp_binding_tf_arr = $binding_tf_1;
    $binding_tf_arr = array();
    for ($i = 0; $i < count($temp_binding_tf_arr); $i++) {
        if (!empty(trim($temp_binding_tf_arr[$i]))) {
            array_push($binding_tf_arr, trim($temp_binding_tf_arr[$i]));
        }
    }
}

if (is_string($chromosome_1)) {
    $chromosome = trim($chromosome_1);
}

if (is_string($upstream_length_1)) {
    $upstream_length = intval(trim($upstream_length_1));
} elseif (is_int($upstream_length_1)) {
    $upstream_length = $upstream_length_1;
} elseif (is_float($upstream_length_1)) {
    $upstream_length = intval($upstream_length_1);
} else {
    $upstream_length = 2000;
}

?>

<!-- Back button -->
<a href="/SoybeanGenVarX/"><button> &lt; Back </button></a>

<br />
<br />

<!-- Get binding TFs -->
<?php
for ($i = 0; $i < count($binding_tf_arr); $i++) {
    echo "<p><b>Queried Binding TF: </b>" . $binding_tf_arr[$i] . "</p>";

    // Get binding TF
    // $query_str = "
    // SELECT M.Motif AS Binding_TF, TF.TF_Family,
    // MS.Chromosome AS Binding_Chromosome, MS.Start AS Binding_Start, MS.End AS Binding_End, MS.Sequence AS Gene_Binding_Sequence,
    // M.Gene, GFF.Chromosome, GFF.Start AS Gene_Start, GFF.End AS Gene_End, GFF.Strand AS Gene_Strand, GFF.Gene_Description
    // FROM (
    //     SELECT Motif, Gene FROM mViz_Soybean_Motif
    //     WHERE Motif = '" . $binding_tf_arr[$i] . "'
    // ) AS M
    // LEFT JOIN mViz_Soybean_TF AS TF
    // ON M.Motif = TF.TF
    // LEFT JOIN (
    //     SELECT Chromosome, Start, End, ID, Name, Sequence FROM mViz_Soybean_" . $chromosome . "_Motif_Sequence
    //     WHERE Name = '" . $binding_tf_arr[$i] . "'
    // ) AS MS
    // ON M.Motif = MS.Name
    // LEFT JOIN (
    //     SELECT ID, Name, Chromosome, Start, End, Strand, Gene_Description,
    //     CASE Strand
    //         WHEN '+' THEN Start-1-" . $upstream_length . "
    //         ELSE End+1
    //     END AS Promoter_Start,
    //     CASE Strand
    //         WHEN '+' THEN Start-1
    //         ELSE End+1+" . $upstream_length . "
    //     END AS Promoter_End
    //     FROM mViz_Soybean_GFF
    //     WHERE Chromosome = '" . $chromosome . "'
    // ) AS GFF
    // ON ((M.Gene = GFF.ID) AND (MS.Chromosome = GFF.Chromosome) AND (MS.Start BETWEEN GFF.Promoter_Start AND GFF.Promoter_End))
    // WHERE ((GFF.Chromosome = '" . $chromosome . "') AND (MS.Chromosome = GFF.Chromosome) AND (MS.Start BETWEEN GFF.Promoter_Start AND GFF.Promoter_End))
    // ORDER BY MS.Chromosome, MS.Start;
    // ";

    // Get binding TF (Optimized MySQL query string)
    $query_str = "
    SELECT M.Motif AS Binding_TF, TF.TF_Family, MS.Chromosome AS Binding_Chromosome,
    MS.Start AS Binding_Start, MS.End AS Binding_End, MS.Sequence AS Gene_Binding_Sequence,
    M.Gene, GFF.Chromosome, GFF.Start AS Gene_Start, GFF.End AS Gene_End,
    GFF.Strand AS Gene_Strand, GFF.Gene_Description
    FROM mViz_Soybean_Motif AS M
    LEFT JOIN mViz_Soybean_TF AS TF
    ON M.Motif = TF.TF
    LEFT JOIN mViz_Soybean_" . $chromosome . "_Motif_Sequence AS MS
    ON M.Motif = MS.Name
    LEFT JOIN (
        SELECT ID, Name, Chromosome, Start, End, Strand, Gene_Description,
        CASE Strand WHEN '+' THEN Start-1-" . $upstream_length . " ELSE End+1 END AS Promoter_Start,
        CASE Strand WHEN '+' THEN Start-1 ELSE End+1+" . $upstream_length . " END AS Promoter_End
        FROM mViz_Soybean_GFF
        WHERE Chromosome = '" . $chromosome . "'
    ) AS GFF
    ON ((M.Gene = GFF.ID) AND (MS.Chromosome = GFF.Chromosome) AND (MS.Start BETWEEN GFF.Promoter_Start AND GFF.Promoter_End))
    WHERE (MS.Chromosome = '" . $chromosome . "') AND (GFF.Chromosome = '" . $chromosome . "') AND (M.Motif = '" . $binding_tf_arr[$i] . "') AND (MS.Name = '" . $binding_tf_arr[$i] . "')
    ORDER BY MS.Chromosome, MS.Start;
    ";

    $stmt = $PDO->prepare($query_str);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $motif_result_arr = pdoResultFilter($result);

    if (isset($motif_result_arr) && !empty($motif_result_arr)){
        echo "<div style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>";
        echo "<table style='text-align:center; border:3px solid #000;'>";

        // Table header
        echo "<tr>";
        foreach ($motif_result_arr[0] as $key => $value) {
            echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
        }
        echo "</tr>";

        // Table body
        for ($j = 0; $j < count($motif_result_arr); $j++) {
            $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
            foreach ($motif_result_arr[$j] as $key => $value) {
                if ($key == "Binding_TF") {
                    echo "<td style=\"border:1px solid black; min-width:80px;\"><a target=\"_blank\" href=\"/SoybeanGenVarX/viewPromoterOnSelectedBindingTF.php?motif=" . $value . "&gene=" . $motif_result_arr[$j]['Gene'] . "&chromosome=" . $motif_result_arr[$j]['Chromosome'] . "&motif_start=" . $motif_result_arr[$j]['Binding_Start'] . "&motif_end=" . $motif_result_arr[$j]['Binding_End'] . "&gene_binding_sequence=" . $motif_result_arr[$j]['Gene_Binding_Sequence'] . "\">" . $value . "</a></td>";
                } else {
                    echo "<td style=\"border:1px solid black; min-width:80px;\">" . $value . "</td>";
                }
            }
            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<br />";
        echo "<br />";
    } else {
        // Display no motif message if none is found
        echo "<div style='width:auto; height:auto; overflow:visible; max-height:1000px;'>";
        echo "No binding TF found in our database!!!";
        echo "</div>";

        echo "<br />";
        echo "<br />";
    }

    echo "<br />";
}

?>

<?php include '../footer.php'; ?>

