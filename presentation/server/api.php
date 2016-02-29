<?php
session_start();
include_once ('config.inc.php');

require 'Slim/Slim.php';
// require 'vendor/autoload.php';
\Slim\Slim::registerAutoloader();

require 'Helpers/DBHelper.php';

$app = new \Slim\Slim();
$db = new \Helpers\DBHelper();

$app->get('/all', 'getContacts');
$app->get('/page/:page', 'getContactsByPage');
$app->get('/contact/:id','getContact');
$app->post('/contact', 'addContact');
$app->put('/contact/:id', 'updateContact');
$app->delete('/contact/:id', 'deleteContact');
$app->get('/count', 'countContacts');
$app->get('/test', 'testConnection');

$app->run();

function getContacts() {
	global $db;
	$app = \Slim\Slim::getInstance();
    $sql = "SELECT id, fname, lname, email, phone FROM agenda where 1 order by fname asc, lname asc";
	$contacts = $db->_get($sql);
	$app->response->setStatus(200);
    $app->response->body(json_encode($contacts));
}

function getContactByPage($page){
	global $db;
	$app = \Slim\Slim::getInstance();
	$start = ()$page - 1) * PERPAGE;
	$sql = "SELECT id, fname, lname, email, phone FROM agenda where 1 order by fname asc, lname asc LIMIT :start, :how;";
	$contacts = $db->_get($sql, ['start' => $start, 'how' => PERPAGE]);
	$app->response->setStatus(200);
    $app->response->body(json_encode($contacts));
}
function getContact($id){
	global $db;
	$app = \Slim\Slim::getInstance();
	$sql = "SELECT * FROM agenda WHERE id=:id;";
	$contact = $db->_select($sql, ['id' => $id]);
	$app->response->setStatus(200);
	$app->response->body(json_encode($contact));
}
function addContact(){
	global $db;
	$app = \Slim\Slim::getInstance();
	$request = $app->request();
	$results = json_decode($request->getBody());
	$sql = "INSERT INTO agenda (fname, lname, email, phone) VALUES (:fname, :lname, :email, :phone);";
	$id = $db->_insert($sql, [
		'fname' => $results->fname,
		'lname' => $results->lname,
		'email' => $results->email,
		'phone' => $results->phone
	]);
	$app->response->setStatus(200);
    $callback = array('success' => $id);
	$app->response->body(json_encode($callback));
}
function updateContact($id){
	global $db;
	$app = \Slim\Slim::getInstance();
	$request = $app->request();
	$results = json_decode($request->getBody());
	$sql = "UPDATE agenda SET fname=:fname, lname=:lname, email=:email, phone=:phone WHERE id=:id;";
	$db->_update($sql, [
		'fname' => $results->fname,
		'lname' => $results->lname,
		'email' => $results->email,
		'phone' => $results->phone,
		'id' => $id
	]);
	$app->response->setStatus(200);
    $callback = array('success' => $id);
	$app->response->body(json_encode($callback));
}
function deleteContact($id){
	global $db;
	$app = \Slim\Slim::getInstance();
	$sql = "DELETE FROM agenda WHERE id=:id;";
	$db->_delete($sql, ['id' => $id]);
	$app->response->setStatus(200);
	$callback = array('success' => $id);
	$app->response->body(json_encode($callback));
}
function countContacts($id){
	global $db;
	$app = \Slim\Slim::getInstance();
	$sql = "SELECT count(id) countContacts FROM agenda;";
	$count = $db->_get($sql);
	$app->response->setStatus(200);
    $app->response->body(json_encode($count));
}

function testConnection() {
    echo json_encode(['success'=>true, 'msg'=>'all ok']);
}
