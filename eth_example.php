<?php

require 'Eth.php';

$eth = new Eth(ETH_RPC_SERVER);
if (!$eth) {
	die();
}

$current_eth_block = $eth->getBlockNumber();
if (!$current_eth_block) {
	die();
}
$erc_tokens['key'] = 'address'; //erc20 token contract address

// echo $current_eth_block . "\n";
$block = $eth->getBlockByNumber($current_eth_block);
if (!$block) {
	die();
}
if (!$block['transactions']) {
	die();
}
foreach ($block['transactions'] as $key => $value) {

	###handle ETH
	$block = $eth->getTransactionByHash($value['transaction_hash']);
	$t_block_number = hexdec($block['blockNumber']);
	if ($block_number >= $t_block_number + 30) {
		$receive_value = wei2eth(hexdec($block['value']));
		echo 'received';
		continue;
	}
	###handle erc20
	if ($erc_tokens) {
		foreach ($erc_tokens as $k => $v) {
			if ($v == $value['to']) {
				$erc_info = $eth->getErc20($value['input']);
				$account = $k . '_' . $erc_info['address'];
				echo 'pending';
			}
		}
	}
}
function sctonum($num, $double = 20) {
	if (false !== stripos($num, "e")) {
		$a = explode("e", strtolower($num));
		$res = bcmul($a[0], bcpow(10, $a[1], $double), $double);
		$res = rtrim($res, '0');
		$res = rtrim($res, '.');
		return $res;
	}
	return $num;
}

function wei2eth($amount) {
	if (!$amount) {
		return 0;
	}

	return sctonum($amount / 1000000000000000000);
}