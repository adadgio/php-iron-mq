<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../test/config.php'); 	// personal config not in repository
require_once(__DIR__.'/../lib/IronMQ.php'); // autoloads BoxApi by itself

use Adadgio\IronMQ\IronMQ;


/**
 * 1. Setup
 */
$ironMQ = new IronMQ($config['apiKey'], $config['projectKey']);

$response = $ironMQ->addSubscriber('my_queue', array('url' => 'http://test.com'));

$response = $ironMQ->removeSubscriber('my_queue', array('url' => 'http://test.com'));


/**
 * 2. Post a message
 *
 */
$response = $ironMQ->postOne('my_queue', array('test' => "YO"));

echo "POST ====<br>";
var_dump($response);


/**
 * 3. Get messages
 *
 */
$messages = $ironMQ->get('my_queue');

echo "<br><br>GET ====<br>";
var_dump($messages);


/**
 * 4. Delete a message
 *
 */
$result = $ironMQ->delete('my_queue', $response->ids[0]);

echo "<br><br>DELETE ====<br>";
var_dump($result);



?>