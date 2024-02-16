<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.plot.ly/plotly-2.12.1.min.js"></script>

<?php
$TITLE = "Soybean Genomic Variations Explorer";

// include '../header.php';
include '../config.php';
include './php/pdoResultFilter.php';
?>

<!-- Get and process the variables -->
<?php
$chromosome = $_GET['chromosome_1'];
$position_start = $_GET['position_start_1'];
$position_end = $_GET['position_end_1'];
$width = $_GET['width_1'];
$strand = $_GET['strand_1'];
$data_option = $_GET['cnv_data_option_1'];
$phenotype = $_GET['phenotype_1'];
$cn_1 = $_GET['cn_1'];

$chromosome = trim($chromosome);
$position_start = intval(trim($position_start));
$position_end = intval(trim($position_end));
$data_option = trim($data_option);
$phenotype = trim($phenotype);
$cn_1 = trim($cn_1);

if (is_string($cn_1)) {
    $temp_cn_array = preg_split("/[;,\n\r]+/", trim($cn_1));
    $cn_array = array();
    for ($i = 0; $i < count($temp_cn_array); $i++) {
        if (!empty(trim($temp_cn_array[$i]))) {
            array_push($cn_array, trim($temp_cn_array[$i]));
        }
    }
} elseif (is_array($cn_1)) {
    $temp_cn_array = $cn_1;
    $cn_array = array();
    for ($i = 0; $i < count($temp_cn_array); $i++) {
        if (!empty(trim($temp_cn_array[$i]))) {
            array_push($cn_array, trim($temp_cn_array[$i]));
        }
    }
}

?>

<!-- Query information -->
<?php
echo "<h3>Queried CNV and Phenotype:</h3>";
echo "<div style='width:auto; height:auto; overflow:visible; max-height:1000px;'>";
echo "<table style='text-align:center; border:3px solid #000;'>";
echo "<tr>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Chromsome</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Position Start</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Position End</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Width</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Strand</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Data Option</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">CN</th>";
echo "<th style=\"border:1px solid black; min-width:80px;\">Phenotype</th>";
echo "</tr>";
echo "<tr bgcolor=\"#DDFFDD\">";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $chromosome . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $position_start . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $position_end . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $width . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $strand . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $data_option . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . implode(',', $cn_array) . "</td>";
echo "<td style=\"border:1px solid black; min-width:80px;\">" . $phenotype . "</td>";
echo "</tr>";
echo "</table>";
echo "<br /><br />";
?>

<h3>Figures:</h3>
<div id="cn_section_div">
    <div id="cn_figure_div">Loading CN plot...</div>
    <div id="cn_summary_table_div">Loading CN summary table...</div>
</div>
<hr>
<div id="improvement_status_summary_figure_div">Loading improvement status summary plot...</div>
<!-- <div id="status_figure_div">Loading status plot...</div> -->
<!-- <div id="improvement_status_figure_div">Loading improvement status plot...</div> -->
<!-- <div id="classification_figure_div">Loading classification plot...</div> -->


<script type="text/javascript" language="javascript" src="./js/viewCNVAndPhenotypeFigures.js"></script>

<script type="text/javascript" language="javascript">
    var chromosome = <?php if(isset($chromosome)) {echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var position_start = <?php if(isset($position_start)) {echo json_encode($position_start, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var position_end = <?php if(isset($position_end)) {echo json_encode($position_end, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var data_option = <?php if(isset($data_option)) {echo json_encode($data_option, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var phenotype = <?php if(isset($phenotype)) {echo json_encode($phenotype, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;
    var cn_array = <?php if(isset($cn_array) && is_array($cn_array) && !empty($cn_array)) {echo json_encode($cn_array, JSON_INVALID_UTF8_IGNORE);} else {echo "";}?>;

    if (chromosome && position_start && position_end && data_option && phenotype && cn_array.length > 0) {
        $.ajax({
            url: './php/queryCNVAndPhenotypeFigures.php',
            type: 'GET',
            contentType: 'application/json',
            data: {
                Chromosome: chromosome,
                Start: position_start,
                End: position_end,
                Data_Option: data_option,
                CN: cn_array,
                Phenotype: phenotype
            },
            success: function (response) {
                res = JSON.parse(response);

                if (res && phenotype) {

                    document.getElementById("improvement_status_summary_figure_div").style.minHeight = "800px";
                    document.getElementById("cn_figure_div").style.minHeight = "800px";
                    // document.getElementById("status_figure_div").style.minHeight = "800px";
                    // document.getElementById("improvement_status_figure_div").style.minHeight = "800px";
                    // document.getElementById("classification_figure_div").style.minHeight = "800px";

                    // Summarize data
                    var result_dict = summarizeQueriedData(
                        JSON.parse(JSON.stringify(res['data'])), 
                        phenotype, 
                        'CN'
                    );

                    var result_arr = result_dict['Data'];
                    var summary_array = result_dict['Summary'];

                    var cnAndImprovementStatusData = collectDataForFigure(result_arr, 'Improvement_Status', 'CN');
                    var cnData = collectDataForFigure(result_arr, phenotype, 'CN');
                    // var statusData = collectDataForFigure(result_arr, phenotype, 'Status');
                    // var improvementStatusData = collectDataForFigure(result_arr, phenotype, 'Improvement_Status');
                    // var classificationData = collectDataForFigure(result_arr, phenotype, 'Classification');

                    plotFigure(cnAndImprovementStatusData, 'CN', 'Improvement_Status_Summary', 'improvement_status_summary_figure_div');
                    plotFigure(cnData, 'CN', 'CN', 'cn_figure_div');
                    // plotFigure(statusData, 'Status', 'Status', 'status_figure_div');
                    // plotFigure(improvementStatusData, 'Improvement_Status', 'Improvement_Status', 'improvement_status_figure_div');
                    // plotFigure(classificationData, 'Classification', 'Classification', 'classification_figure_div');

                    // Render summarized data
                    document.getElementById('cn_summary_table_div').innerText = "";
                    document.getElementById('cn_summary_table_div').innerHTML = "";
                    document.getElementById('cn_summary_table_div').appendChild(
                        constructInfoTable(summary_array)
                    );
                    document.getElementById('cn_summary_table_div').style.overflow = 'scroll';
                }
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                document.getElementById('cn_figure_div').innerText="";
                document.getElementById('cn_summary_table_div').innerHTML="";
                document.getElementById('improvement_status_summary_figure_div').innerHTML="";
                // document.getElementById('status_figure_div').innerHTML="";
                // document.getElementById('improvement_status_figure_div').innerHTML="";
                // document.getElementById('classification_figure_div').innerHTML="";
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "CN distribution figure is not available due to lack of data!!!";
                document.getElementById('cn_figure_div').appendChild(p_tag);
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "CN summary table is not available due to lack of data!!!";
                document.getElementById('cn_summary_table_div').appendChild(p_tag);
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "Improvement status summary figure is not available due to lack of data!!!";
                document.getElementById('improvement_status_summary_figure_div').appendChild(p_tag);
                // var p_tag = document.createElement('p');
                // p_tag.innerHTML = "Status distribution figure is not available due to lack of data!!!";
                // document.getElementById('status_figure_div').appendChild(p_tag);
                // var p_tag = document.createElement('p');
                // p_tag.innerHTML = "Improvement status distribution figure is not available due to lack of data!!!";
                // document.getElementById('improvement_status_figure_div').appendChild(p_tag);
                // var p_tag = document.createElement('p');
                // p_tag.innerHTML = "Classification distribution figure is not available due to lack of data!!!";
                // document.getElementById('classification_figure_div').appendChild(p_tag);
            }
        });
    } else {
        document.getElementById('cn_figure_div').innerText="";
        document.getElementById('cn_summary_table_div').innerHTML="";
        document.getElementById('improvement_status_summary_figure_div').innerHTML="";
        // document.getElementById('status_figure_div').innerHTML="";
        // document.getElementById('improvement_status_figure_div').innerHTML="";
        // document.getElementById('classification_figure_div').innerHTML="";
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "CN distribution figure is not available due to lack of data!!!";
        document.getElementById('cn_figure_div').appendChild(p_tag);
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "CN summary table is not available due to lack of data!!!";
        document.getElementById('cn_summary_table_div').appendChild(p_tag);
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "Improvement status summary figure is not available due to lack of data!!!";
        document.getElementById('improvement_status_summary_figure_div').appendChild(p_tag);
        // var p_tag = document.createElement('p');
        // p_tag.innerHTML = "Status distribution figure is not available due to lack of data!!!";
        // document.getElementById('status_figure_div').appendChild(p_tag);
        // var p_tag = document.createElement('p');
        // p_tag.innerHTML = "Improvement status distribution figure is not available due to lack of data!!!";
        // document.getElementById('improvement_status_figure_div').appendChild(p_tag);
        // var p_tag = document.createElement('p');
        // p_tag.innerHTML = "Classification distribution figure is not available due to lack of data!!!";
        // document.getElementById('classification_figure_div').appendChild(p_tag);
    }

</script>
