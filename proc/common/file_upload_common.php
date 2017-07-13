<?php
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/file/FileAttachDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/FileDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$fileDAO = new FileAttachDAO();
$dao = new FileDAO();

$up_detail_path = "";
if($fb->fb['type'] == 'user' && $fb->fb['ftype'] == 'order'){
	$up_detail_path = ORDER_TEMP_FILE;
}

$save_file_path = ORDER_TEMP_FILE . "/" .date('Y') . date('m'). date('d')."/";
$up_detail_path = $fileDAO->getFilePath($up_detail_path);
$path = $fileDAO->createPath($up_detail_path);

// 5 minutes execution time
@set_time_limit(5 * 60);


$targetDir=$path;
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
	$real_fname = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
	$real_fname = $_FILES["file"]["name"];
} else {
	$real_fname = $fb->fb['order_no']."_".$fb->fb['prd_detail_no'];
}
$ext = $fileDAO->getExt($real_fname);

$fileName = $fb->fb['prd_detail_no'];//date('Ymdhis') . "_" . mt_rand(100, 999);

if($ext) $fileName = $fileName.".".$ext;
$filePath = $targetDir.$fileName;


// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	// Strip the temp .part suffix off
	rename("{$filePath}.part", $filePath);
	$param['save_file_name'] = $fileName;
	$param['origin_file_name'] = $real_fname;
	$param['size'] = null;
	$param['order_no'] = $fb->fb['order_no'];
	$param['prd_detail_no'] = $fb->fb['prd_detail_no'];
	$param['dvs'] = $fb->fb['dvs'];
	$param['type'] = $fb->fb['type'];
	$param['ftype'] = $fb->fb['ftype'];
	$param['file_path'] = $save_file_path;

	$dao->setOrderImg($conn,$param);
}

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
