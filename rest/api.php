<?php
session_start();
include_once( 'config.inc.php' );

require 'Slim/Slim.php';
// require 'vendor/autoload.php';
\Slim\Slim::registerAutoloader();

require 'Helpers/DBHelper.php';

$app = new \Slim\Slim();
$db = new DBHelper();

$app->get( '/all/', 'getContacts' );
$app->get('/page/', 'getContactsByPage');
$app->get( '/page/:page', 'getContactsByPage' );
$app->post( '/pagefull/', 'getContactsByPageFull' );
$app->get( '/contact/:id', 'getContact' );
$app->post( '/contact/', 'addContact' );
$app->put( '/contact/:id', 'updateContact' );
$app->delete( '/contact/:id', 'deleteContact' );
$app->get( '/count/', 'countContacts' );
$app->get( '/test/', 'testConnection' );

$app->run();

function getContacts()
{
    global $db;
    $app = \Slim\Slim::getInstance();
    $sql = "SELECT id, fname, lname, email, phone FROM agenda where 1 order by fname asc, lname asc";
    $contacts = $db->_get( $sql );
    if ( count( $contacts ) > 0 ) {
        $app->response->setStatus( 200 );
        $app->response->body( json_encode( $contacts ) );
    } else {
        $app->response->getStatus( 404 );
        $app->response->body( json_encode( [ 'error' => TRUE, 'msg' => 'no contacts' ] ) );
    }
}

function getContactsByPage( $page = 0 )
{
    global $db;
    $count = getCount();
    $page = $page + 1 -1;
    if ($page == 0 || ($page -1)  > $count / PERPAGE) $page = 1;
    $app = \Slim\Slim::getInstance();
    $start = ( $page - 1 ) * PERPAGE;
    $sql = "SELECT id, fname, lname, email, phone FROM agenda order by fname, lname asc LIMIT $start, " . PERPAGE;
    //echo  $sql . $start;
    $contacts = $db->_get( $sql );
    if ( count( $contacts ) > 0 ) {
        $app->response->setStatus( 200 );
        $app->response->body( json_encode( [ 'Count' => $count, 'Items' => $contacts ] ) );
    } else {
        $app->response->getStatus( 404 );
        $app->response->body( json_encode( [ 'error' => TRUE, 'msg' => 'no contacts' ] ) );
    }
}

function getContactsByPageFull()
{
    $request = \Slim\Slim::getInstance()->request();
    $body = $request->getBody();
    $obj = json_decode($body);
    //var_dump($obj);exit;
    if ($obj == '') {
        getContactsByPage(0);
        return;
    }
    global $db;
    $page = $obj->page;
    $search_str = $obj->search;
    $sortby = !isset($obj->sortby) ? '' : $obj->sortby;
    $orderby = !isset($obj->order) ? '' : $obj->order;
    if (trim($search_str) != '') {
        $where = " WHERE fname LIKE '%$search_str%' OR lname LIKE '%$search_str%' OR email LIKE '%$search_str%' OR phone LIKE '%$search_str%' ";
    } else {
        $where = '';
    }
    if (empty($order) OR !isset($order)) {
        $order = true;
    }
    if (trim($sortby) != '') {
        $sort = " ORDER BY $sortby";
        if ($order) {
            $sort .= " DESC ";
        } else {
            $sort .= " ASC ";
        }
    } else {
        $sort = "ORDER BY fname, lname ASC ";
    }
    $count = getCount($where . $sort);
    $page = $page + 1 -1;
    if ($page == 0 || ($page -1)  > $count / PERPAGE) $page = 1;
    $app = \Slim\Slim::getInstance();
    $start = ( $page - 1 ) * PERPAGE;
    $sql = "SELECT id, fname, lname, email, phone FROM agenda $where $sort LIMIT $start, " . PERPAGE;
    //echo  $sql . $start;
    $contacts = $db->_get( $sql );
    if ( count( $contacts ) > 0 ) {
        $app->response->setStatus( 200 );
        $app->response->body( json_encode( [ 'Count' => $count, 'Items' => $contacts ] ) );
    } else {
        $app->response->getStatus( 404 );
        $app->response->body( json_encode( [ 'error' => TRUE, 'msg' => 'no contacts' ] ) );
    }
}

function getContact( $id )
{
    global $db;
    $app = \Slim\Slim::getInstance();
    $sql = "SELECT * FROM agenda WHERE id=:id;";
    $contact = $db->_get( $sql, [ 'id' => $id ] );
    if ( !empty( $contact ) ) {
        $app->response->setStatus( 200 );
        $app->response->body( json_encode( $contact ) );
    } else {
        $app->response->getStatus( 404 );
        $app->response->body( json_encode( [ 'error' => TRUE, 'msg' => 'no data' ] ) );
    }
}

function addContact()
{
    global $db;
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $results = json_decode( $request->getBody() );
    $sql = "INSERT INTO agenda (fname, lname, email, phone) VALUES (:fname, :lname, :email, :phone);";
    $id = $db->_insert( $sql, [
        'fname' => $results->fname,
        'lname' => $results->lname,
        'email' => $results->email,
        'phone' => $results->phone
    ] );
    $app->response->setStatus( 200 );
    $callback = array ( 'success' => $id );
    $app->response->body( json_encode( $callback ) );
}

function updateContact( $id )
{
    global $db;
    $app = \Slim\Slim::getInstance();
    $request = $app->request();
    $results = json_decode( $request->getBody() );
    $sql = "UPDATE agenda SET fname=:fname, lname=:lname, email=:email, phone=:phone WHERE id=:id;";
    $db->_update( $sql, [
        'fname' => $results->fname,
        'lname' => $results->lname,
        'email' => $results->email,
        'phone' => $results->phone,
        'id'    => $id
    ] );
    $app->response->setStatus( 200 );
    $callback = array ( 'success' => $id );
    $app->response->body( json_encode( $callback ) );
}

function deleteContact( $id )
{
    global $db;
    $app = \Slim\Slim::getInstance();
    $sql = "DELETE FROM agenda WHERE id=:id;";
    $db->_delete( $sql, [ 'id' => $id ] );
    $app->response->setStatus( 200 );
    $callback = array ( 'success' => $id );
    $app->response->body( json_encode( $callback ) );
}

function getCount($sub_sql = '')
{
    global $db;
    $sql = "SELECT count(id) as countContacts FROM agenda $sub_sql;";
    $count = $db->_get( $sql );

    return $count[0]['countContacts'];
}

function countContacts()
{
    $app = \Slim\Slim::getInstance();
    $count = getCount();
    $app->response->setStatus( 200 );
    $app->response->body( json_encode( ['countContacts' => $count] ) );
}

function testConnection()
{
    echo json_encode( [ 'success' => TRUE, 'msg' => 'all ok' ] );
}
