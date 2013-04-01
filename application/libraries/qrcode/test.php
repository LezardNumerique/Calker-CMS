<?php

	$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

	$PNG_WEB_DIR = 'temp/';

	include "qrlib.php";

	if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);

	$filename = $PNG_TEMP_DIR.'test.png';

	$errorCorrectionLevel = 'L';
	if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];

	$matrixPointSize = 4;
	if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);

	$adress = '
	Les Voiles d\'Antibes
	17 Rue Andréossy
	06600 ANTIBES
	Tél : 04 93 34 42 47
	Email : contact@voiles-antibes.com
    ';

	QRcode::png($adress, $filename, $errorCorrectionLevel, $matrixPointSize, 2);

	echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';
?>