<?php

$fileName = '1.csv';
unset($lines);
$lines = file($fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
# print_r($lines);

$pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';

$emails = array();

foreach ($lines as $line_num => $line) {
    # if ($line_num == 0) continue;

    # echo $line.PHP_EOL;
	
	preg_match_all($pattern, $line, $matches);
	# if (!empty($matches[0])) $emails[] = trim($matches[0]);
	if (!empty($matches[0])) {
		# print_r($matches[0]);
		foreach ($matches[0] as $item) {
			$item = trim($item);
			if (!empty($item)) $emails[] = $item;
		}
	}

}

echo 'all items count: '.count($emails).PHP_EOL;
if (!empty($emails)) $emails = array_unique($emails);
sort($emails);
echo 'all unique items count: '.count($emails).PHP_EOL;
# print_r($emails);

echo implode(PHP_EOL, $emails);