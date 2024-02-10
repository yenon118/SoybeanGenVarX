<?php
$TITLE = "Soybean Genomic Variations Explorer";
include '../header.php';
?>

<div>
    <table width="100%" cellspacing="14" cellpadding="14">
        <tr>
            <td><h2>Promoter Search</h2></td>
        </tr>
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllPromotersByGenes.php" method="get" target="_blank">
                    <h2>Search By Gene IDs</h2>
                    <br />
                    <label for="gene_name_1"><b>Gene IDs:</b> (eg Glyma.01G049100 Glyma.01G049200 Glyma.01G049300)</label>
                    <textarea id="gene_name_1" name="gene_name_1" rows="10" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01G049100&#10;Glyma.01G049200&#10;Glyma.01G049300"></textarea>
                    <br />
                    <br />
                    <label for="upstream_length_1"><b>Upstream length (bp):</b> (eg 2000)</label>
                    <input type="text" id="upstream_length_1" name="upstream_length_1" size="60">
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllPromotersByBindingTFs.php" method="get" target="_blank">
                    <h2>Search By Binding TFs</h2>
                    <br />
                    <label for="binding_tf_1"><b>Binding TFs:</b> (eg Glyma.01G005500 Glyma.01G022500 Glyma.01G023500)</label>
                    <textarea id="binding_tf_1" name="binding_tf_1" rows="8" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01G005500&#10;Glyma.01G022500&#10;Glyma.01G023500"></textarea>
                    <br />
                    <br />
                    <label for="chromosome_1"><b>Gene Binding Chromosome:</b></label>
                    <select name="chromosome_1" id="chromosome_1">
                        <option value="Chr01">Chr01</option>
                        <option value="Chr02">Chr02</option>
                        <option value="Chr03">Chr03</option>
                        <option value="Chr04">Chr04</option>
                        <option value="Chr05">Chr05</option>
                        <option value="Chr06">Chr06</option>
                        <option value="Chr07">Chr07</option>
                        <option value="Chr08">Chr08</option>
                        <option value="Chr09">Chr09</option>
                        <option value="Chr10">Chr10</option>
                        <option value="Chr11">Chr11</option>
                        <option value="Chr12">Chr12</option>
                        <option value="Chr13">Chr13</option>
                        <option value="Chr14">Chr14</option>
                        <option value="Chr15">Chr15</option>
                        <option value="Chr16">Chr16</option>
                        <option value="Chr17">Chr17</option>
                        <option value="Chr18">Chr18</option>
                        <option value="Chr19">Chr19</option>
                        <option value="Chr20">Chr20</option>
                    </select>
                    <br />
                    <br />
                    <label for="upstream_length_1"><b>Upstream length (bp):</b> (eg 2000)</label>
                    <input type="text" id="upstream_length_1" name="upstream_length_1" size="60">
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <br />
                <br />
            </td>
        </tr>
        <tr>
            <td><h2>Copy Number Variation Search</h2></td>
        </tr>
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllCNVByGenes.php" method="get" target="_blank">
                    <h2>Search by Gene IDs</h2>
                    <br />
                    <label for="gene_id_2"><b>Gene IDs:</b><span>&nbsp;(eg Glyma.01G000100 Glyma.02G001700 Glyma.03G018100)</span></label>
                    <textarea id="gene_id_2" name="gene_id_2" rows="12" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01G000100&#10;Glyma.02G001700&#10;Glyma.03G018100"></textarea>
                    <br />
                    <br />
                    <label for="cnv_data_option_2"><b>Data Option:</b></label>
                    <select name="cnv_data_option_2" id="cnv_data_option_2">
                        <option value="Individual_Hits">Individual Hits</option>
                        <option value="Consensus_Regions" selected>Consensus Regions</option>
                    </select>
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllCNVByAccessionAndCopyNumbers.php" method="get" target="_blank">
                    <h2>Search By Accession and Copy Numbers</h2>
                    <br />
                    <label for="accession_2"><b>Accession:</b> (eg PI_479752)</label>
                    <input type="text" id="accession_2" name="accession_2" size="60">
                    <br />
                    <br />
                    <label for="copy_number_2"><b>Copy Numbers:</b> (eg CN0 CN1 CN2 CN3 CN4 CN5 CN6 CN7 CN8)</label>
                    <textarea id="copy_number_2" name="copy_number_2" rows="10" cols="50" placeholder="&#10;Please separate each copy number into a new line. &#10;&#10;Example:&#10;CN0&#10;CN1&#10;CN3&#10;&#10; * CN2 represents normal.&#10;** CN2 is not in individual hits dataset."></textarea>
                    <br />
                    <br />
                    <label for="cnv_data_option_2"><b>Data Option:</b></label>
                    <select name="cnv_data_option_2" id="cnv_data_option_2">
                        <option value="Individual_Hits">Individual Hits</option>
                        <option value="Consensus_Regions" selected>Consensus Regions</option>
                    </select>
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr>
        <tr>
        <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllCNVByChromosomeAndRegion.php" method="get" target="_blank">
                    <h2>Search By Chromosome and Region</h2>
                    <br />
                    <label for="chromosome_2"><b>Chromosome:</b> (eg Chr01)</label>
                    <input type="text" id="chromosome_2" name="chromosome_2" size="60">
                    <br />
                    <br />
                    <label for="position_start_2"><b>Starting Position:</b> (eg 41175001)</label>
                    <input type="text" id="position_start_2" name="position_start_2" size="60">
                    <br />
                    <br />
                    <label for="position_end_2"><b>Ending Position:</b> (eg 41775000)</label>
                    <input type="text" id="position_end_2" name="position_end_2" size="60">
                    <br />
                    <br />
                    <label for="cnv_data_option_2"><b>Data Option:</b></label>
                    <select name="cnv_data_option_2" id="cnv_data_option_2">
                        <option value="Individual_Hits">Individual Hits</option>
                        <option value="Consensus_Regions" selected>Consensus Regions</option>
                    </select>
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr>
        <tr>
            <td>
                <br />
                <br />
            </td>
        </tr>
        <!-- <tr>
            <td><h2>Transposable Element Search</h2></td>
        </tr>
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllTransposableElementsByGenes.php" method="get" target="_blank">
                    <h2>Search by Gene IDs</h2>
                    <br />
                    <label for="gene_id_3"><b>Gene IDs</b><span>&nbsp;(eg Glyma.01G000100 Glyma.02G001700 Glyma.03G018100)</span></label>
                    <textarea id="gene_id_3" name="gene_id_3" rows="12" cols="50" placeholder="&#10;Please separate each gene into a new line. &#10;&#10;Example:&#10;Glyma.01G000100&#10;Glyma.02G001700&#10;Glyma.03G018100"></textarea>
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllTransposableElementsByElements.php" method="get" target="_blank">
                    <h2>Search by Elements</h2>
                    <br />
                    <label for="element_3"><b>Elements</b><span>&nbsp;(eg rnd-1_family-184 rnd-1_family-241 rnd-1_family-237)</span></label>
                    <textarea id="element_3" name="element_3" rows="12" cols="50" placeholder="&#10;Please separate each element into a new line. &#10;&#10;Example:&#10;rnd-1_family-184&#10;rnd-1_family-241&#10;rnd-1_family-237&#10;rnd-1_family-103"></textarea>
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr>
        <tr>
            <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <form action="viewAllTransposableElementsByChromosomeAndRegion.php" method="get" target="_blank">
                    <h2>Search By Chromosome and Region</h2>
                    <br />
                    <label for="chromosome_3"><b>Chromosome:</b> (eg Chr01)</label>
                    <input type="text" id="chromosome_3" name="chromosome_3" size="60">
                    <br />
                    <br />
                    <label for="position_start_3"><b>Starting Position:</b> (eg 41175001)</label>
                    <input type="text" id="position_start_3" name="position_start_3" size="60">
                    <br />
                    <br />
                    <label for="position_end_3"><b>Ending Position:</b> (eg 41775000)</label>
                    <input type="text" id="position_end_3" name="position_end_3" size="60">
                    <br />
                    <br />
                    <input type="submit" value="Search">
                </form>
            </td>
        </tr> -->
    </table>
    <br />
    <br />
</div>

<div style="margin-bottom:40px;" align="center">
    <button onclick="downloadUserManual()" style="margin-right:20px;">Download User Manual</button>
</div>

<hr />

<br />
<br />

<div>
    <table width="100%" cellspacing="14" cellpadding="14">
        <tr>
            <td align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
                <h2>If you use the Soybean Genomic Variations Explorer in your work, please cite:</h2>
                <br />
                <p> Chan YO, Biova J, Mahmood A, Dietz N, Bilyeu K, Škrabišová M, Joshi T: <b> Genomic Variations Explorer (GenVarX): A Toolset for Annotating Promoter and CNV Regions Using Genotypic and Phenotypic Differences. </b> Frontiers in Genetics 2023, In Press. </p>
            </td>
        </tr>
    </table>
</div>

<script type="text/javascript" language="javascript" src="./js/index.js"></script>

<script type="text/javascript" language="javascript">
</script>

<?php include '../footer.php'; ?>
