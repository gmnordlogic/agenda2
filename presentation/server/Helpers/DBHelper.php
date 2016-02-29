<?php
session_start();
include_once ('config.inc.php');

namespace Helpers/DBHelper;

class DBHelper extends PDO {
	
	public $db;

	public function __construct() {
		$this->db = new PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8", DBUSER, DBPASS);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function _delete($sql, $params = null) {
		try {
			$stmt = $this->db->prepare($sql);
			if (is_array($params)) {
				foreach($params as $key=>$value){
					$stmt->bindValue($key, $value);
				}
			}
			$stmt->execute();

			return;
		} catch(PDOException $e) {
			$e->customErrorType='err_delete_failed';
			throw $e;
			return false;
		}
	}

	public function _insert($sql, $params = null, $stopOnError = true) {
		try {
			$stmt = $this->db->prepare($sql);
			if (is_array($params)) {
				foreach($params as $key=>$value){
					$stmt->bindValue($key, $value);
				}
			}
			$stmt->execute();
			$result = $this->db->lastInsertId();

			return $result;
		} catch(PDOException $e) {
			$e->customErrorType='err_insert_failed';
			throw $e;
			if($stopOnError) die();
			return false;
		}
	}

	public function _update($sql, $params = null, $stopOnError = true) {
		try {
			$stmt = $this->db->prepare($sql);
			if (is_array($params)) {
				foreach($params as $key=>$value){
					$stmt->bindValue($key, $value);
				}
			}
			$stmt->execute();
			$result = $stmt->rowCount();

			return $result;
		} catch(PDOException $e) {
			$e->customErrorType='err_update_failed';
			throw $e;
			if($stopOnError) die();
			return false;
		}
	}

	public function _get($sql, $params = null) {
		try {
			$stmt = $this->db->prepare($sql);
			if (is_array($params)) {
				foreach($params as $key=>$value){
					$stmt->bindValue($key, $value);
				}
			}
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return $result;
		} catch(PDOException $e) {
			$e->customErrorType='err_select_failed';
			throw $e;
		}
	}

}
