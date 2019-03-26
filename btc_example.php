<?php

require 'Btc.php';

$btc = new Btc(BTC_RPC_SERVER, BTC_RPC_USERNAME, BTC_RPC_PASSWORD);
if (!$btc) {
	die();
}

$current_block = $btc->getblockcount();
if (!$current_block) {
	die();
}
$block_hash = $btc->getblockhash($current_block);
if (!$block_hash['result']) {
	sleep(360);
}
$block = $btc->getblock($block_hash['result']);
if (!$block['result']['tx']) {
	die();
}

foreach ($block['result']['tx'] as $key => $value) {
	$transaction = $btc->gettransaction($value);
	if ($transaction['confirmations'] > 5) {
		print_r($v);
	}
}
