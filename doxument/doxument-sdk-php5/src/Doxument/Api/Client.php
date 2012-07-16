<?php
/**
 * Doxument REST API Client PHP5 Library
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @category Doxument
 * @author $Author: support@doxument.com $
 * @copyright Copyright (c) 2011 Doxument.com (http://www.doxument.com)
 * @license http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @link http://www.doxument.com
 * @version $Date: 2012-04-16 10:38:48 +0300 (Mon, 16 Apr 2012) $
 */

class Doxument_Api_Client {
	const ENDPOINT = 'http://api.doxument.com/v1';
	const GET = 'GET';
	const POST = 'POST';
	const DELETE = 'DELETE';
	const PUT = 'PUT';
	const HEAD = 'HEAD';

	/**
	 *
	 * Doxument API Key
	 * @var string
	 */
	protected $apiKey;

	/**
	 *
	 * Doxument API Token
	 * @var string
	 */
	protected $apiToken;

	/**
	 *
	 * Doxument API Endpoint
	 * @var string
	 */
	protected $endpoint = self::ENDPOINT;

	/**
	 *
	 * Proxy to use if any, e.g. http://localhost:8888
	 * @var string
	 */
	protected $proxy;

	/**
	 *
	 * cURL resource
	 * @var resource
	 */
	protected $ch;

	/**
	 *
	 * HTTP response headers from the last request
	 * @var array
	 */
	protected $headers;

	/**
	 *
	 * Constructor
	 * @param string $apiKey
	 * @param string $apiToken
	 */
	public function __construct($apiKey, $apiToken) {
		$this->init();
		$this->apiKey = $apiKey;
		$this->apiToken = $apiToken;
	}

	/**
	 *
	 * @return void
	 */
	protected function init() {
		if(!extension_loaded('curl')) {
			throw new Doxument_Api_Exception('cURL extension is not loaded');
		}
		if(!extension_loaded('json')) {
			throw new Doxument_Api_Exception('JSON extension is not loaded');
		}
	}

	/**
	 *
	 * Set $apiKey. Provides a fluent interface.
	 *
	 * @param string $apiKey
	 * @return Doxument_Api_Client
	 */
	public function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
		return $this;
	}

	/**
	 *
	 * Set $apiToken. Provides a fluent interface.
	 *
	 * @param string $apiToken
	 * @return Doxument_Api_Client
	 */
	public function setApiToken($apiToken) {
		$this->apiToken = $apiToken;
		return $this;
	}

	/**
	 *
	 * Set $endpoint. Provides a fluent interface.
	 *
	 * @param string $endpoint
	 * @return Doxument_Api_Client
	 */
	public function setEndpoint($endpoint) {
		$this->endpoint = $endpoint;
		return $this;
	}

	/**
	 *
	 * Set $proxy. Provides a fluent interface.
	 *
	 * @param string $proxy
	 * @return Doxument_Api_Client
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
		return $this;
	}

	/**
	 *
	 * Get info about document content w/o downloading it
	 * @param string $id document id
	 * @return array HTTP headers
	 */
	public function info($id) {
		$this->shutdown();
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_HEADER, true);
		curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, array($this, 'readHeader'));
		$this->execute(sprintf('/docs/%s', $id), null, self::HEAD);
		$headers = $this->headers;
		$this->headers = null;
		return $headers;
	}

	/**
	 *
	 * Get a document record by id.
	 *
	 * @param string $id document id
	 * @param array $params any extra params to pass
	 * @return array JSON decoded message
	 */
	public function get($id, array $params = null) {
		$json = $this->execute(sprintf('/docs/%s.json', $id), $params);
		return json_decode($json, true);
	}
	
	/**
	 *
	 * Convert a document to another format.
	 *
	 * @param string $id document id
	 * @param string $format to convert to
	 * @param array $params extra params: callback. array('callback' => 'http://yourapp.com/listen.php')
	 * @return array JSON decoded message
	 */
	public function convert($id, $format, $params) {
		$json = $this->execute(sprintf('/docs/%s.%s', $id, $format), $params);
		return json_decode($json, true);
	}

	/**
	 * Returns a document status
	 *
	 * @param string $id document id
	 * @return string document status
	 */
	public function check($id) {
		$data = $this->get($id);
		if($data['success'] == 'true') {
			return $data['payload']['doc']['status'];
		}
	}

	/**
	 *
	 * Get a collection of documents
	 *
	 * @param array $params[optiona] extra request params, e.g. array('offset' => 10, 'limit' => 5)
	 * @return array
	 */
	public function getAll(array $params = null) {
		$json = $this->execute('/docs.json', $params);
		return json_decode($json, true);
	}

	/**
	 *
	 * Uploads a file.
	 *
	 * @param string $file path to a file to upload
	 * @return array new document record
	 */
	public function upload($file) {
		$params = array(
			'file' => '@'.realpath($file)
		);
		$json = $this->execute('/docs.json', $params, self::POST);
		return json_decode($json, true);
	}

	/**
	 *
	 * Download a file. Supports HTTP range requests
	 *
	 * @param string $id document id
	 * @param string $file [optiona] filename save to. defaults to /your system tmp dir/$id
	 * @param array $params [optiona] extra query params: size, crop, etc. example: array('size' => 200, 'crop' => true)
	 * @param string $range [optiona] HTTP range to download. Example: 0-1024, 4096-
	 *
	 * @return string local file path to a download file
	 */
	public function download($id, $file = null, array $params = null, $range = null) {
		if($file === null) {
			$file = sys_get_temp_dir().'/'.$id;
		}

		$this->shutdown();
		$this->ch = curl_init();
		if(($fp = fopen($file, 'wb')) === false) {
			throw new Doxument_Api_Exception('Unable to open a file pointer for writing.');
		}
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, false); // direct the output to file pointer
		curl_setopt($this->ch, CURLOPT_FILE, $fp);
		curl_setopt($this->ch, CURLOPT_BINARYTRANSFER, true);
		if($range !== null) {
			curl_setopt($this->ch, CURLOPT_RANGE, $range);
		}
		$this->execute(sprintf('/docs/%s', $id), $params);
		fclose($fp);
			
		return $file;
	}

	/**
	 *
	 * Delete document record and content.
	 *
	 * @param string $id document id
	 * @return boolean
	 */
	public function delete($id) {
		return $this->execute(sprintf('/docs/%s.json', $id), null, self::DELETE) ? true : false;
	}

	/**
	 *
	 * Execute HTTP request.
	 *
	 * @param string $path URL path
	 * @param array $params[optional] request params
	 * @param string $method[optional] HTTP method. defaults: GET
	 * @param array $headers[optional] array of header in cURL format
	 * 
	 * @return string|boolean JSON string on success, false on failure
	 *
	 * @throws Doxument_Api_Exception
	 */
	public function execute($path, array $params = null, $method = self::GET, array $headers = null) {
		$url = rtrim($this->endpoint, '/').'/'.ltrim($path, '/');

		$options = array(
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_USERAGENT => __CLASS__,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
			CURLOPT_USERPWD => $this->apiKey . ':' . $this->apiToken
		);

		if(!$this->ch) {
			$this->ch = curl_init();
			$options += array(
				CURLOPT_HEADER => false,
				CURLOPT_RETURNTRANSFER => true,
			);	
		}
		curl_setopt_array($this->ch, $options);

		$proxy = $this->proxy;
		if($proxy) {
			$proxy = parse_url($proxy);
			curl_setopt($this->ch, CURLOPT_PROXY, $proxy['host']);
			if(array_key_exists('port', $proxy)) {
				curl_setopt($this->ch, CURLOPT_PROXYPORT, $proxy['port']);
			}
		}

		switch($method) {
			case self::HEAD:
				curl_setopt($this->ch, CURLOPT_NOBODY, 1);
				break;
					
			case self::POST:
				curl_setopt($this->ch, CURLOPT_POST, true);
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
				break;

			case self::DELETE:
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
				 
			case self::GET:
				if(!empty($params)) {
					$url .= '?'.http_build_query($params);
				}
				break;
		}

		if(!empty($headers)) {
			url_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($this->ch, CURLOPT_URL, $url);

		$result = curl_exec($this->ch);

		if($result === false) {
			$errorNumber = curl_errno($this->ch);
			$error = curl_error($this->ch);
			curl_close($this->ch);

			throw new Doxument_Api_Exception($errorNumber . ': ' . $error);
		}

		$code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		if(!in_array($code, array('200', '201', '204', '206', '302'))) {
			throw new Doxument_Api_Exception($result);
		}

		curl_close($this->ch);

		// prepare for next request
		$this->ch = curl_init();

		return $result;
	}
	 
	/**
	 * CURL callback function for reading and processing headers
     * 
     * @param resource $ch
     * @param string $header
     * @return integer
     */
    protected function readHeader($ch, $header) {
    	preg_match('/([^:]+):\s*(.*)/', $header, $matches);
    	if(isset($matches[1]) && isset($matches[2])) {
    		$this->headers[strtolower($matches[1])] = $matches[2];
    	}
        return strlen($header);
    }
    	
    /**
     * 
     * Close cURL resource.
     * @return void
     */
	public function shutdown() {
		if(is_resource($this->ch)) {
			curl_close($this->ch);
		}
	}  
	  
	/**
	 * 
	 * destructor
	 * @return void
	 */
	public function __destruct() {
		$this->shutdown();
	}
}
