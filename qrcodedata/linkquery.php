<?php
$querydata = array();
foreach ( glob ("*.json", GLOB_NOSORT)  as  $file ) $querydata[str_replace(".json", "", $file)] = file_get_contents($file);
echo json_encode($querydata);