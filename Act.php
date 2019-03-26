<?php
/**
 *
 */
use JsonRPC\Client;

class Act {

	var $client;
	function __construct($rpc_server) {
		if (!$this->client) {
			$this->client = new Client($rpc_server);
			$this->client->authentication(ACT_RPC_USERNAME, ACT_RPC_PASSWORD);
		}
	}

	private function exec($procedure, $param = []) {
		try {
			$res = $this->client->execute($procedure, $param);
			return $res;
		} catch (JsonRPC\Exception\ConnectionFailureException $e) {
			$this->_log($e->getMessage());
			return false;
		} catch (JsonRPC\Exception\ResponseException $e) {
			$this->_log($e->getMessage());
			return false;
		}

	}

	/**
	 * @desc 获取当前区块编号
	 */
	function getBlockNumber() {
		$result = $this->exec('blockchain_get_block_count');
		return hexdec($result);
	}

	/**
	 * @desc 根据编号获取块内容
	 */
	function getBlockByNumber($block_number) {
		if (!$block_number) {
			return false;
		}
		$result = $this->exec('blockchain_get_block', [$block_number]);
		return $result;
	}

	function getEvent($block_number, $trx_id) {
		$result = $this->exec('blockchain_get_events', [$block_number, $trx_id]);
		return $result;
	}
	/**
	 * @desc 获取当前网络内容
	 */
	function getInfo() {
		$result = $this->exec('get_info');
		return $result;

	}

	/**
	 * @desc 新建钱包
	 */

	function createWallet($name, $password) {
		$result = $this->exec('wallet_create', ['wallet_name' => $name, 'password' => $password]);
		return $result;
	}

	function walletInfo() {
		$result = $this->exec('wallet_get_info');
		return $result;
	}

	/**
	 * @desc 打开钱包
	 */
	function openWallet($name) {
		$result = $this->exec('wallet_open', [$name]);
		return $result;
	}

	/**
	 * @desc 解锁钱包
	 */
	function unlockWallet($timeout, $password) {
		$result = $this->exec('wallet_unlock', [$timeout, $password]);
		return $result;
	}

	/**
	 * @desc 获取某个账户的交易记录
	 */
	function getAccountTransaction($name) {
		$this->openWallet(ACT_WALLET_USERNAME);
		$result = $this->exec('wallet_account_transaction_history', [$name]);
		return $result;
	}

	/**
	 * @desc 获取交易费
	 */
	function getTransactionFee() {
		$this->openWallet(ACT_WALLET_USERNAME);
		$result = $this->exec('wallet_get_transaction_fee');
		return $result;
	}
	/**
	 * @desc 发起交易信息
	 */
	function transferToAddress($from_account_name, $to_address, $amount, $message = '') {
		$this->openWallet(ACT_WALLET_USERNAME);
		$this->unlockWallet(120, ACT_WALLET_PASSWORD);
		$result = $this->exec('wallet_transfer_to_address', [
			$amount, 'ACT', $from_account_name, $to_address, $message,
		]);
		return $result;
	}

	/**
	 * @desc创建账户
	 */
	function createAccount($username) {
		$this->openWallet(ACT_WALLET_USERNAME);
		$this->unlockWallet(120, ACT_WALLET_PASSWORD);
		$result = $this->exec('wallet_account_create', [$username]);
		return $result;
	}

	/**
	 * @desc 获取交易信息
	 */
	function getTransaction($transaction_id) {
		$result = $this->exec('wallet_get_transaction', [$transaction_id]);
		return $result;
	}

	/**
	 * @desc 获取某个账户的余额
	 */
	function getAccountBalance($account) {
		$this->openWallet(ACT_WALLET_USERNAME);
		$result = $this->exec('wallet_account_balance', [$account]);
		return $result;
	}

	/**
	 * @desc获取交易的结果ID
	 */
	function getContractResult($trx_id) {
		$result = $this->exec('blockchain_get_contract_result', [$trx_id]);
		return $result;
	}

	/**
	 * @desc 调用合约
	 */
	function callContract($contract_address, $account_name, $function_name, $param, $asset_symbol, $cost_ceil = 1) {
		$result = $this->exec('call_contract', [$contract_address, $account_name, $function_name, $param, $asset_symbol, $cost_ceil]);
		return $result;
	}

	function transferBin($from_account_name, $to_address, $amount) {
		$result = $this->callContract(BIN_CONTRACT_ADDRESS, $from_account_name, 'transfer_to', $to_address . '|' . $amount, 'ACT', 1);
		return $result;
	}

	/**
	 * @desc 获取交易信息
	 */
	function blockchainTransaction($trx_id) {
		if (!$trx_id) {
			return false;
		}

		$result = $this->exec('blockchain_get_transaction', [$trx_id]);
		return $result;
	}

	/**
	 * @desc 合约交易信息
	 */
	function prettyTransaction($trx_id) {
		$result = $this->exec('blockchain_get_pretty_contract_transaction', [$trx_id]);
		return $result;
	}

	/**
	 * @desc 内部数值转换成ACT
	 */
	function toAct($number) {
		return $number / 100000;
	}

	function _log($msg) {
		$err = date('Y-m-d H:i:s') . "\n";
		$err .= $msg . "\n";
		$err .= "---------------------------\n";
		error_log($err, 3, '/home/log/act_rpc.log');
	}
}