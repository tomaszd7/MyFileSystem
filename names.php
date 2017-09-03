<?php

require_once('./vendor/autoload.php');

//var_dump(NormalizeSpanishWords::normalize('Eric Clapton - Tears In Heaven (lyrics y subtitulos en español).mp3'));
//die();
//$folder = '/media/tomasz/X-DEPO/płyta\ nr\ 2';
$folder = './data/';
$outputFolder = './output/';
if (isset($argv[1])) {
    $folder = $argv[1];
}

$filesSystem = new MyFileSystem($folder, null, new NormalizeSpanishWords());
//$filesSystem = new MyFileSystem($folder, null, null);
$filesSystem->copyFiles();
//$filesSystem->renameFiles();
$filesSystem->showSummary();


//    var_dump($name);
//    die();
//	if (strlen($name) > 10) {
//            if (mb_detect_encoding($name->basename) == 'UTF-8') {
//                $name = NormalizeSpanishWords::normalizeString($name->basename);
////                $name = mb_convert_encoding($name, 'ASCII');
//                $goodList[] = mb_detect_encoding($name) . '==> '. $name;
//            }
//		
//	}


 

