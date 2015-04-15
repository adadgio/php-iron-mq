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
	 *
	 */
	protected $errors = array();

	/**
	 * 
	 *
	 */
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;

		$this->apiUrl = "https://mq-aws-us-east-1.iron.io/1/projects";
	}
	
	/**
	 * 
	 *
	 */
	public function setProject($projectKey)
	{
		$this->projectKey = $projectKey;

		return $this;
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
	public function post($queue, $message)
	{
		$url = $this->apiUrl."/{$this->projectKey}/queues/{$queue}/messages";

		return $this->curlRequest($url, "POST", $message);
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
	 * Make a GET, POST or DELETE CURL request
	 * 
	 */
	public function formatMessage($messageData)
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
	public function curlRequest($url, $verb = "GET", $messageData = array())
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

			$postBody = array(
				'messages' => array( array('body' => $this->formatMessage($messageData)) )
			);

			curl_setopt_array($curl, array(
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => json_encode($postBody)
			));

		} elseif($verb === "DELETE") {

			curl_setopt_array($curl, array(
				CURLOPT_CUSTOMREQUEST => "DELETE",
			));

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