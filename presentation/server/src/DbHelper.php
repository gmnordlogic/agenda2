<?php
class DbHelper extends PDO {

  public $app;

  public function setApp($app) {
      $this->app = $app;
  }

  public function _deleteFromDB($sql, $params = null) {
      try {
          $stmt = $this->prepare($sql);
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
      }
  }

  public function _pushToDB($sql, $params = null, $stopOnError = true) {
      try {
          $stmt = $this->prepare($sql);
          if (is_array($params)) {
              foreach($params as $key=>$value){
                  $stmt->bindValue($key, $value);
              }
          }
          $stmt->execute();
          $result = $this->lastInsertId();
          return $result;
      } catch(PDOException $e) {
          $e->customErrorType='err_push_failed';
          throw $e;
          if($stopOnError) die();
          return false;
      }
  }

  public function _pushUpdatesToDB($sql, $params = null, $stopOnError = true) {
      try {
          $stmt = $this->prepare($sql);
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

  public function _getFromDB($sql, $params = null) {

      try {
          $stmt = $this->prepare($sql);
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
