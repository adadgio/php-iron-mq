<?php

namespace RomainBruckert\IronMQ;

use Exception;

/**
 * Box View API unofficial PHP wrapper
 *
 * Docs: 
 *
 * @author Romain Bruckert
 */
class IronMQ
{
	/**
	 * IronMQ API url
	 */
	private $apiUrl;

	/**
	 * Your IronMQ API key
	 */
	private $apiKey;

	/**
	 * Your IronMQ projectKey identifier
	 */
	private $projectKey;


	/**
	 * Your IronMQ subscribers information
	 */
	private $subscribers;

	/**
	 *
	 */
	protected $errors = array();

	
	/**
	 * 
	 * 
	 */
	public function __construct($config = array())
	{
		if(!isset($config['api_url']) OR !isset($config['api_key']) OR !isset($config['project_key'])) {
			throw new Exception("Config requires three valid parameters: <api_url>, <api_key> and <project_key>.");
		}

		$this->apiUrl = $config['api_url'];

		$this->apiKey = $config['api_key'];

		$this->projectKey = $config['project_key'];
	}

	/**
	 * 
	 *
	 */
	public function get($queue)
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/messages";

		return $this->curlRequest($url, "GET");
	}


	/**
	 * 
	 *
	 */
	public function postOne($queue, $message)
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/messages";

		$options = array(
			'messages' => array(
				array('body' => $this->format($message))
			)
		);

		return $this->curlRequest($url, "POST", $options);
	}


	/**
	 * 
	 *
	 */
	public function postMany($queue, $messages = array())
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/messages";

		$options = array('messages' => array());

		foreach($messages as $message) {
			$options['messages'][] = array('body' => $this->format($message));
		}

		return $this->curlRequest($url, "POST", $options);
	}


	/**
	 * 
	 *
	 */
	public function delete($queue, $messageId)
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/messages/{$messageId}";

		return $this->curlRequest($url, "DELETE");
	}
	
	/**
	 * 
	 * 
	 */
	public function addSubscriber($queue, $subscriber)
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/subscribers";

		$options = array(
			'subscribers' => array(
				$subscriber
			)
		);

		return $this->curlRequest($url, "POST", $options);
	}

	/**
	 * 
	 * 
	 */
	public function removeSubscriber($queue, $subscriber)
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/subscribers";
		
		return $this->curlRequest($url, "DELETE", array('subscribers' => array($subscriber)));
	}


	/**
	 * Make a GET, POST or DELETE CURL request
	 * 
	 */
	public function format($messageData)
	{
		if(is_string($messageData)) {

			return $messageData;

		} elseif(is_numeric($messageData)) {

			return (string) $messageData;

		} elseif(is_object($messageData) OR is_array($messageData)) {

			return json_encode($messageData);

		} else
		{
			return $messageData;
		}
	}


	/**
	 * Make a GET, POST or DELETE CURL request
	 * 
	 */
	public function curlRequest($url, $verb = "GET", $options = array())
	{
		// check project key 
		if(empty($this->projectKey)) {
			throw new Exception("Project id needs not set.");
		}

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
				"Authorization: OAuth {$this->apiKey}"
			)
		));
		
		if($verb === "POST") {

			curl_setopt($curl, CURLOPT_POST, 1);
			
			if(!empty($options)) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($options));
			}

		} elseif($verb === "DELETE") {

			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");

			if(!empty($options)) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($options));
			}

		} else
		{
			// its a GET request
		}

		$result = curl_exec($curl);

		$curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);

        if($curl_errno > 0 OR !empty($curl_error)) {
        	throw new Exception("CURL error [{$curl_errno}]: {$curl_error}");
        }

        return json_decode($result);
	}

}