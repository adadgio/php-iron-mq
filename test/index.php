<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../test/config.php'); 	// personal config not in repository
require_once(__DIR__.'/../lib/IronMQ.php'); // autoloads BoxApi by itself

use RomainBruckert\IronMQ\IronMQ;


/**
 * 1. Setup
 */
$ironMQ = new IronMQ($config['apiKey']);
$ironMQ->setProject($config['projectKey']);


/**
 * 2. Post a message
 *
 */
$response = $ironMQ->post('my_queue', array('test' => "YO"));

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