<?php
require_once("../core/includes/bootstrap.func.php");

$postRequest = array(
	"ReportFileName"=>post('ReportFileName'),
	"OutputFormat"=>post('OutputFormat'), 
	"OutputFilename"=>post('OutputFilename'),
);
if(isset($_POST['Parameter_0_Name'])){	$postRequest["Parameters[0].Name"] = post('Parameter_0_Name');}
if(isset($_POST['Parameter_0_Value'])){	$postRequest['Parameters[0].Value'] = post('Parameter_0_Value');}
if(isset($_POST['Parameter_1_Name'])){	$postRequest["Parameters[1].Name"] = post('Parameter_1_Name');}
if(isset($_POST['Parameter_1_Value'])){	$postRequest['Parameters[1].Value'] = post('Parameter_1_Value');}
if(isset($_POST['Parameter_2_Name'])){	$postRequest["Parameters[2].Name"] = post('Parameter_2_Name');}
if(isset($_POST['Parameter_2_Value'])){	$postRequest['Parameters[2].Value'] = post('Parameter_2_Value');}
if(isset($_POST['Parameter_3_Name'])){	$postRequest["Parameters[3].Name"] = post('Parameter_3_Name');}
if(isset($_POST['Parameter_3_Value'])){	$postRequest['Parameters[3].Value'] = post('Parameter_3_Value');}
if(isset($_POST['Parameter_4_Name'])){	$postRequest["Parameters[4].Name"] = post('Parameter_4_Name');}
if(isset($_POST['Parameter_4_Value'])){	$postRequest['Parameters[4].Value'] = post('Parameter_4_Value');}

$resource = curl_init();
if($postRequest['OutputFormat'] == "blob"){
	$url = _TOOLS_SITE . 'cpdf/Crystal/RunReportPdf';
	curl_setopt($resource, CURLOPT_URL, $url);
	curl_setopt($resource, CURLOPT_POSTFIELDS, $postRequest);
	curl_setopt($resource, CURLOPT_HEADER, 1);
	curl_setopt($resource, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($resource, CURLOPT_BINARYTRANSFER, 1);
	$file = curl_exec($resource);
	curl_close($resource);	
	
	$file_array = explode("\n\r", $file, 2);
	$header_array = explode("\n", $file_array[0]);
	foreach($header_array as $header_value) {
		$header_pieces = explode(':', $header_value);
		if(count($header_pieces) == 2) {
			$headers[$header_pieces[0]] = trim($header_pieces[1]);
		}
	}
	header('Content-type: ' . $headers['Content-Type']);
	header('Content-Disposition: ' . $headers['Content-Disposition']);
	echo substr($file_array[1], 1);
}
if($postRequest['OutputFormat'] == "file"){
	$url = _TOOLS_SITE . 'cpdf/Crystal/RunReportPdfToDisk';
	curl_setopt($resource, CURLOPT_URL, $url);	
	curl_setopt($resource, CURLOPT_POSTFIELDS, $postRequest);
	$file = curl_exec($resource);
	curl_close($resource);		
	return $file;
}
?>