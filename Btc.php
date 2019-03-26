<?php
class Btc {

	var $client;
	function __construct($rpc_server, $username, $password) {
		if (!$this->client) {
			$this->client = new \Nbobtc\Http\Client('http://' . $username . ':' . $password . '@' . $rpc_server);
			$this->client->getRequest()->withHeader('Connection', 'Keep-Alive');
		}
	}
	private function exec(\Nbobtc\Command\Command $command) {
		try {
			$response = $this->client->sendCommand($command);
			$output = json_decode($response->getBody()->getContents(), true);
			return $output;
		} catch (Exception $e) {
			$this->_log($e->getMessage());
			return false;
		}
	}

	function getnewaddress($account) {
		$command = new \Nbobtc\Command\Command('getnewaddress', $account);
		return $this->exec($command);
	}

	function getreceivedbyaddress($address, $minconf = 6) {
		$command = new \Nbobtc\Command\Command('getreceivedbyaddress', [$address, $minconf]);
		return $this->exec($command);
	}

	function getaddressesbyaccount($account) {
		$command = new \Nbobtc\Command\Command('getaddressesbyaccount', $account);
		return $this->exec($command);
	}

	function sendtoaddress($address, $amount) {
		$command = new \Nbobtc\Command\Command('sendtoaddress', [$address, $amount]);
		return $this->exec($command);
	}

	function getbalance($account, $minconf = 6) {

		$command = new \Nbobtc\Command\Command('getbalance', [$account, $minconf]);
		return $this->exec($command);
	}

	/**
	 * @desc 根据交易hash，获取交易信息
	 */
	function gettransaction($hash) {
		$command = new \Nbobtc\Command\Command('gettransaction', $hash);
		return $this->exec($command);
	}

	/**
	 * @desc 获取交易的原始数据
	 */
	function getrawtransaction($hash) {
		$command = new \Nbobtc\Command\Command('getrawtransaction', $hash);
		return $this->exec($command);
	}

	/**
	 * @desc 解析原始的交易信息
	 */
	function decoderawtransaction($hash) {
		$command = new \Nbobtc\Command\Command('decoderawtransaction', $hash);
		return $this->exec($command);
	}
	/**
	 * @desc 根据区块高度，获取区块hash
	 */
	function getblockhash($blocknumber) {
		$command = new \Nbobtc\Command\Command('getblockhash', $blocknumber);
		return $this->exec($command);
	}

	/**
	 * @desc 根据区块hash，获取区块内容
	 */
	function getblock($hash) {
		$command = new \Nbobtc\Command\Command('getblock', $hash);
		return $this->exec($command);
	}

	/**
	 * @desc 获取当前区块高度
	 */
	function getblockcount() {
		$command = new \Nbobtc\Command\Command('getblockcount');
		return $this->exec($command);
	}

	function listunspent() {
		$command = new \Nbobtc\Command\Command('listunspent', 6);
		return $this->exec($command);

	}

	function createrawtransaction($input, $output) {
		$command = new \Nbobtc\Command\Command('createrawtransaction', [$input, $output]);
		return $this->exec($command);
	}

	function signrawtransaction($hex) {
		$command = new \Nbobtc\Command\Command('signrawtransaction', $hex);
		return $this->exec($command);
	}

	function sendrawtransaction($hex) {
		$command = new \Nbobtc\Command\Command('sendrawtransaction', $hex);
		return $this->exec($command);
	}
	private function _log($msg) {
		$err = date('Y-m-d H:i:s') . "\n";
		$err .= $msg . "\n";
		$err .= "---------------------------\n";
		error_log($err, 3, '/home/log/btc_rpc.log');
	}
}