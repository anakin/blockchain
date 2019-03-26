<?php
require 'Usdt.php';

$usdt = new Usdt(USDT_RPC_SERVER, USDT_RPC_USERNAME, USDT_RPC_PASSWORD);

$address = $block_address;
$balance = $usdt->getBalance($address);
if (!isset($balance['result']['balance'])) {
	die();
}
$balance = $balance['result']['balance'];
echo $balance;
