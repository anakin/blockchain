<?php

require 'Act.php';

$act = new Act(ACT_RPC_SERVER);

$current_block = $act->getBlockNumber();
if (!$current_block) {
	exit;
}

$block = $act->getBlockByNumber($current_block);
if (!$block) {
	sleep(10);
}

if (!count($block['user_transaction_ids'])) {
	die();
}

foreach ($block['user_transaction_ids'] as $key => $value) {
	$trx = $act->blockchainTransaction($value);
	$trx = $trx[1]['trx'];
	//确认是act交易
	if ($trx['act_account'] && 0 == $trx['act_inport_asset']['asset_id'] && ('deposit_op_type' == $trx['operations'][0]['type'] || 'withdraw_op_type' == $trx['operations'][0]['type'])) {
		//交易数量
		$amount = $trx['act_inport_asset']['amount'] / 100000;
	}

	//合约交易
	if ('transaction_op_type' == $trx['operations'][0]['type']) {
		$sub_trx = $trx['operations'][0]['data']['trx'];
		if ($sub_trx['act_account']) {
			$amount = $sub_trx['act_inport_asset']['amount'] / 100000;
		}
	}
}
