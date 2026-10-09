<?php
// Test Excel generator syntax
function test_excel_export_xml() {
    $rows = [
        [
            'sno' => 1,
            'dept_name' => 'Computer Science & Engineering',
            'dept_code' => 'CSE',
            'name' => 'SWATHI JILLA',
            'designation' => 'HOD & Assoc. Prof',
            'role_category' => 'HOD',
            'is_hod' => 'Yes (Active HOD)',
            'is_rnd' => 'No',
            'qualification' => 'M.Tech',
            'jntuh_reg_id' => '2717-150427-180153',
            'experience' => '15 Years',
            'email' => 'swathijilla@tcek.in',
            'phone' => '9848000002',
            'status' => 'Active'
        ]
    ];
    
    $clean = function($val) {
        $s = (string)$val;
        if (isset($s[0]) && in_array($s[0], ['=', '+', '-', '@'], true)) {
            $s = "'" . $s;
        }
        return htmlspecialchars($s, ENT_XML1, 'UTF-8');
    };
    
    ob_start();
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
    ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Header">
   <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#FFFFFF" ss:Bold="1"/>
   <Interior ss:Color="#00B894" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="Data">
   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#1E293B"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Faculty Directory">
  <Table>
   <Row ss:StyleID="Header">
    <Cell><Data ss:Type="String">S.No</Data></Cell>
    <Cell><Data ss:Type="String">Department</Data></Cell>
    <Cell><Data ss:Type="String">Faculty Name</Data></Cell>
    <Cell><Data ss:Type="String">Designation</Data></Cell>
    <Cell><Data ss:Type="String">Role Category</Data></Cell>
    <Cell><Data ss:Type="String">HOD Status</Data></Cell>
    <Cell><Data ss:Type="String">R&amp;D Affiliation</Data></Cell>
   </Row>
   <?php foreach ($rows as $r): ?>
   <Row ss:StyleID="Data">
    <Cell><Data ss:Type="Number"><?php echo $r['sno']; ?></Data></Cell>
    <Cell><Data ss:Type="String"><?php echo $clean($r['dept_name']); ?></Data></Cell>
    <Cell><Data ss:Type="String"><?php echo $clean($r['name']); ?></Data></Cell>
    <Cell><Data ss:Type="String"><?php echo $clean($r['designation']); ?></Data></Cell>
    <Cell><Data ss:Type="String"><?php echo $clean($r['role_category']); ?></Data></Cell>
    <Cell><Data ss:Type="String"><?php echo $clean($r['is_hod']); ?></Data></Cell>
    <Cell><Data ss:Type="String"><?php echo $clean($r['is_rnd']); ?></Data></Cell>
   </Row>
   <?php endforeach; ?>
  </Table>
 </Worksheet>
</Workbook>
    <?php
    $xml = ob_get_clean();
    return $xml;
}

$xml = test_excel_export_xml();
$valid = simplexml_load_string($xml);
echo "XML Valid: " . ($valid !== false ? "YES" : "NO") . "\n";
