<?php 

$rootDir = '/app';
$logs = '';

$dir = $rootDir.'/images/';
$f1 = isset($_GET['test'])?$dir.$_GET['test']:'';
$f2 = isset($_GET['with'])?$dir.$_GET['with']:'';
$threshold = isset($_GET['t'])?$_GET['t']:1;
$callBack = isset($_GET['callback'])?$_GET['callback']:null;

define ('PROBABILITY_LIMIT', $threshold );
define ('SKIP_ALREADY_MATCHED', isset($_GET['skip-matched']) );

$exampleLinl1 = 'http://localhost:8080/?test=test/*&with=known/*&debug';
$exampleLink2 = 'http://localhost:8080/?test=test/*&with=test/clapton*';

if(!$f1 || !$f2){
	echo "<html><head></head><body>";
	echo "Please provide additional parameters:\n</br></br>";
	echo " - test: (string) The set of files to be tested\n</br>";
	echo " - with: (string) Original, known file\n</br>";
	echo " - t: (threshold, default:1) Limit the probability between 0 - X, (e.g for 2 will return 0-2, deafault is 0-1)\n</br>";
	echo " - callback: (URL) If provided, POST request with the results will be sent to notify\n</br>";
	echo " - skip-matched: (default: false) - Useful when comparing multiple matching images. Skip already matched test images when comparing with the next set of base images.\n</br>";
	echo " - debug: (default: false) - Used for formatted output for debugging only\n\n</br></br>";
	echo "EXAMPLES:\n</br></br>";
	echo "Images from different folders: </br><a href='".$exampleLinl1."'>$exampleLinl1</a>\n</br></br>";
	echo "Images within same folder, with filter: </br><a href='".$exampleLink2."'>$exampleLink2</a>\n</br></br>";
	echo "Provide callback URL to receive results (optional) e.g.:</br><a href='".$exampleLinl1."&callback=http://example.com/receive-results'>$exampleLinl1&callback=http://example.com/receive-results</a>\n</br></br>";
	echo "Check app/log.txt file for more details";
	echo "</body></html>";
	return;
}

function ago($time) { 
    return secondsToTime(getSeconds($time));
}

function secondsToTime($timediff){
	$timestring = '';

    $days=intval($timediff/86400);
    $remain=$timediff%86400;
    $hours=intval($remain/3600);
    $remain=$remain%3600;
    $mins=intval($remain/60);
    $secs=$remain%60;

    if ($secs>=0) $timestring = "0m".$secs."s";
    if ($mins>0) $timestring = $mins."m".$secs."s";
    if ($hours>0) $timestring = $hours."u".$mins."m";
    if ($days>0) $timestring = $days."d".$hours."u";

    return $timestring; 
}

function getSeconds($time){
	return time()-$time; 
}

function getFilesList($filePath){
	if(isImage($filePath)){
		return array($filePath);
	}
$command = '
cd ..;
cd ..;
cd root/openface;
for n in '.$filePath.'; do echo "$n"; done
';
$results = shell_exec($command);
$filesTmp = explode(PHP_EOL, $results);
$files = array();

foreach ($filesTmp as $file) {
	if(isImage($file)){
			$files[] = $file;
	}
}

return $files;
}

function compare($path1, $path2){
	$command = '
	cd ..;
	cd ..;
	cd root/openface;
	./demos/compare.py '.$path1.' '.$path2.';
	';
	return shell_exec($command);
}
function isImage($path){
	return 
	contains($path, '.jpg') || 
	contains($path, '.jpeg') || 
	contains($path, '.gif') || 
	contains($path, '.bmp') || 
	contains($path, '.png');
}
function contains($haystack, $needle){
	if (strpos($haystack, $needle) !== false) {
	    return true;
	}
	return false;
}

function sendPost($data){
	global $callBack;
	if(!$callBack){
		return;
	}
	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($data)
	    )
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($callBack, false, $context);
	if ($result === FALSE) { /* Handle error */ }
	//var_dump($result);
}

function saveToLog($results){
	//Save to logs for detailed information
	global $rootDir;
	$file = $rootDir.'/logs.txt';
	$content = file_get_contents($file);
	//$content = ''; //clear previous logs
	$content .= "/////////////////////////////////////////////////\n";
	$content .= "            ".date("D M d, Y G:i")."   \n";
	$content .= "/////////////////////////////////////////////////\n";
	$content .= $results."\n";
	file_put_contents($file, $content);
}

function getFileNameFromPath($path){
	return end(split('/',$path));
}

function sortFinalArray(&$arr){
	$sorted = array();
	//Sort by probabilities from higher to lower:
	foreach ($arr as $key => $item) {
		uasort($item, function ($a, $b) {
			$result = 0;
			if($b > $a){
				return -1;
			}else if($b < $a){
				return 1;
			}
			return $result;
		});
		$sorted[$key] = $item;
	}
	$arr = $sorted;
}

/**
* Returns an array if matched, an empty array() if not, and null if
* no facesa are found
*/
function parseResults($results){
	//Parse results, split to lines
	$lines = explode(PHP_EOL, $results);
	$data = array();
	$files = null;
	global $totalMatches;

	foreach ($lines as $line) {
	//If line that contains file names
		if(0 === strpos($line, 'Unable to find a face')){
			return null;
		} else if(0 === strpos($line, 'Comparing')){
			$parts = explode(' ', $line);
			$file1path = $parts[1];
			$file2path = rtrim($parts[3],".");

			//Use only filenames from file paths
			$f1 = getFileNameFromPath($file1path);
			$f2 = getFileNameFromPath($file2path);

			//craete an array of filenames
			$files = $f1;
		}
		//If line that contains probability result
		else if(-1 !== strpos($line, 'Squared')){
			$parts = explode(' ', $line);

			//If probability value is set
			if(isset($parts[8]) && $parts[8]){

				//If we have previously stored the values of the file names, that means that the next line is this line containing probability
				if($files){
					$probability = floatval($parts[8]);
					if($probability <= PROBABILITY_LIMIT){ //show only files that are good match (0-1). More than 1 is more likely that the result is not correct
						//$files['result'] = $probability;
						//$data[] = $files;	
						$data = array('image'=>$files,'result'=>$probability);
						$alreadyMatched[$files] = $files;
						$totalMatches++;
					}else{
						//no match
						return array();
					}
				}
				$files = null;
			}
		}
	}
	return $data;
}

function getTotalTestImages(){
	global $testFiles;
	global $notFaces;
	return sizeof($originalFiles) - sizeof($notFaces);
}

function getTotalBaseImages(){
	global $originalFiles;
	return sizeof($originalFiles);
}

//*************************************
// BOOTSTRAP LOGIC
//*************************************

//Get files from test folder
$testFiles = getFilesList($f1);
$originalFiles = getFilesList($f2);
$notFaces = array(); //Skip images that are not faces in the next iterations. 
$alreadyMatched = array(); //Skip images that are already matched in the next iterations.
$totalMatches = 0;
$totalImagesPerSet = sizeof($testFiles);
$totalImages = $totalImagesPerSet * sizeof($originalFiles);
$progressCounter = 0;
$set = 0;
$image = 0;
/*print_r($testFiles);
print_r($originalFiles);
return;*/

$timeProcessing = time();
error_log("\n\n\n===================================\nCOMPARISON STARTED");
error_log('Comparing faces of '.sizeof($testFiles).' test images with '.sizeof($originalFiles). ' base images...');

$finalArr = array();

foreach ($originalFiles as $originalFile) {
	$set++;
	$image = 0;
	$finalArr[getFileNameFromPath($originalFile)] = array();
	foreach ($testFiles as $testFile) {
		$image++;
		$progressCounter++;
		if(in_array($testFile, $notFaces, true)){
			//Skip processing for non images
			//Useful when having more than one iteration (multiple )
			continue;
		}
		if(in_array($testFile, $alreadyMatched, true)){
			//Skip processing for test images that are previously matched with other base images
			//Useful when having more than one iteration (multiple)
			//Note: be careful, it may be false positive match, depending on threshold
			continue;
		}
		error_log("\n\n--------------------------\n");
		error_log("\nComparing: ".$testFile. ' with '. $originalFile);
		$results = compare($testFile, $originalFile);
		$parsedArr = parseResults($results);
		$resultText = '';
		if(!is_array($parsedArr)){
			//If no faces are detected in this image, skip it from the next iteration
			$notFaces[$testFile] = $testFile;
			$resultText = "No faces";
		}else if(sizeof($parsedArr) == 0){
			$resultText = "No match";
		}else{
			$finalArr[getFileNameFromPath($originalFile)]
			[$parsedArr['image']] = 
			$parsedArr['result'];
			$resultText = 'Match - '. $parsedArr['result'];
			if(SKIP_ALREADY_MATCHED){
				$alreadyMatched[$testFile] = $testFile;
			}
		}
		
		$logs .= $results;
		$progressStr = "\nResults: ".$resultText;
		$progressStr .= "\nProgress (".(intval($progressCounter/$totalImages*100)).'%):';
		$progressStr .= ' image '.$image.'/'.sizeof($testFiles).' of set '.$set.'/'.sizeof($originalFiles)."\n";
		$progressStr .= "Matches: ".$totalMatches.'/'.$totalImages."\n";
		$secsTillNow = getSeconds($timeProcessing);
		$processedImgCount = $totalImagesPerSet*($set-1) + $image;
		$progressStr .= "Estimate:".secondsToTime(($secsTillNow*$totalImages/$processedImgCount)-$secsTillNow);
		
		error_log($progressStr);
	}
}

sortFinalArray($finalArr);


$processingTime = ago($timeProcessing);

error_log("\n\nRESULTS:\nTime: ".$processingTime."\nMatches: ".$totalMatches.'/'.$totalImages."\n");

$logs .= "> Processing time: ". $processingTime."\n";

//Save information to logs
saveToLog($logs);

//Send data to provided callback url
sendPost($finalArr);

//DEBUGGING:
if(isset($_GET['debug'])){
	echo '<pre>',print_r($finalArr,1),'</pre>';
	return;
}
echo json_encode($finalArr);