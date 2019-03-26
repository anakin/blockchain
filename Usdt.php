<?php
class Usdt {

	var $client;
	var $propertyid = 31;
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

	/**
	 * @desc 获取某个地址的余额
	 */
	function getBalance($address) {
		$command = new \Nbobtc\Command\Command('omni_getbalance', [$address, $this->propertyid]);
		return $this->exec($command);
	}

	/**
	 * @desc 获取一个新的地址
	 */
	function getnewaddress($account) {
		$command = new \Nbobtc\Command\Command('getnewaddress', $account);
		return $this->exec($command);
	}

	/**
	 * @desc 转账
	 */
	function send($from_address, $to_address, $amount) {
		$command = new \Nbobtc\Command\Command('omni_send', [$from_address, $to_address, $this->propertyid, $amount]);
		return $this->exec($command);
	}

}