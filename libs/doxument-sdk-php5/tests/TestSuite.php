<?php
class TestSuite {
	/**
	 * 
	 * @var Doxument_Api_Client
	 */
	protected $client;
	
	public function __construct(Doxument_Api_Client $client) {
		$this->client = $client;	
	}
	
	protected function preCall($name) {
		echo "Calling ". $name ." method\n";	
	}
	
	protected function postCall($data) {
		if(is_bool($data)) {
			echo $data ? "OK" : "FAILURE";
			return;
		}
		if(is_string($data)) {
			echo $data."\n";
			return;
		}
		print_r($data);
	}
	
	public function __call($name, $args) {
		$this->preCall($name);
		try {
			$data = call_user_func_array(array($this->client, $name), $args);
		} catch (Doxument_Api_Exception $ex) {
			die($ex);
		}
		$this->postCall($data);
		return $data;		
	}
	
	public function runAll() {
		$filename = dirname(__FILE__).'/../data/test.txt';
		$data = $this->upload($filename);
		$id = $data['payload']['doc']['id'];
		$this->info($id);
		$this->get($id);
		$this->download($id, $filename.'.'.uniqid());		
//		$this->download($id, $filename.'.'.uniqid(), '5-10');		
//		$this->download($id, $filename.'.'.uniqid(), '5-');
		$this->convert($id, 'pdf');		
		$this->getAll();
//		$this->delete($id);
	}
}

