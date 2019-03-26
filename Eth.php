<?php
/**
 *
 */
use JsonRPC\Client;

class Eth {

	var $client;
	function __construct($rpc_server) {
		if (!$this->client) {
			$this->client = new Client($rpc_server);
		}
	}

	private function exec($procedure, $param = '') {
		try {
			if ($param) {
				$res = $this->client->execute($procedure, $param);
			} else {
				$res = $this->client->execute($procedure);
			}
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
		$result = $this->exec('eth_blockNumber');
		return hexdec($result);
	}

	/**
	 * @desc 根据编号获取区块内容
	 */
	function getBlockByNumber($block_number) {
		if (!$block_number) {
			return false;
		}
		$block_number = '0x' . dechex($block_number);
		$result = $this->exec('eth_getBlockByNumber', [$block_number, true]);
		return $result;
	}

	/**
	 * @desc 新建钱包地址
	 */
	function newAccount($code) {
		if (!$code) {
			return false;
		}
		$result = $this->exec('personal_newAccount', [$code]);
		return $result;
	}

	function getBlockByHash($hash) {
		if (!$hash) {
			return false;
		}

		$result = $this->exec('eth_getBlockByHash', [$hash, true]);
		return $result;
	}

	function getTransactionByHash($hash) {
		if (!$hash) {
			return false;
		}

		$result = $this->exec('eth_getTransactionByHash', [$hash]);
		return $result;
	}

	/**
	 * @发起交易
	 */
	function sendTransaction($from, $from_code, $to, $value, $input = "0x") {
		if (!$from || !$from_code || !$to || !$value) {
			return false;
		}
		$value = '0x' . dechex($value * 1000000000000000000);
		$this->exec('personal_unlockAccount', [$from, $from_code, 60]);
		$result = $this->exec('eth_sendTransaction', [['from' => $from, 'to' => $to, 'value' => $value, 'input' => $input]]);
		return $result;
	}

	/**
	 * @计算需要的gas
	 */
	function estimateGas($from, $to, $value) {
		if (!$from || !$to || !$value) {
			return false;
		}
		$value = '0x' . dechex($value * 1000000000000000000);
		$result = $this->exec('eth_estimateGas', [['from' => $from, 'to' => $to, 'value' => $value]]);
		return hexdec($result);
	}

	/**
	 * @gas的价格
	 */
	function gasPrice() {
		$result = $this->exec('eth_gasPrice');
		$result = Util::sctonum(hexdec($result) / 1000000000000000000);
		return $result;
	}

	/**
	 * @获取账户余额
	 */
	function getBalance($account) {
		if (!$account) {
			return false;
		}
		$result = $this->exec('eth_getBalance', [$account, 'latest']);
		return Util::sctonum(hexdec($result) / 1000000000000000000);
	}

	function getErc20($input) {
		if (strlen($input) < 128) {
			return false;
		}

		$method = substr($input, 0, 10);
		if ('0xa9059cbb' != $method) {
			return false;
		}

		$address = ltrim(substr($input, 10, 64), 0);
		$amount = ltrim(substr($input, 74, 64), 0);
		$amount = Util::sctonum(hexdec($amount) / 1000000000000000000);
		return [
			'address' => $address,
			'amount' => $amount,
		];
	}

		/**
	 * @获取合约币的数量
	 */
	function contractBalance($address, $contract) {

		$data = '0x70a08231' . sprintf("%'.064s", substr($address, 2));
		$result = $this->exec('eth_call', [['from' => $address, 'to' => $contract, 'data' => $data], 'latest']);
		$result = Util::sctonum(hexdec($result) / 1000000000000000000);
		return $result;
	}
	function _log($msg) {
		$err = date('Y-m-d H:i:s') . "\n";
		$err .= $msg . "\n";
		$err .= "---------------------------\n";
		error_log($err, 3, '/home/log/eth_rpc.log');
	}
}