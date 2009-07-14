<?php

/**
 * Power
 * 
 * @author Jonathan Borzilleri
 */
class Power extends BasePower
{

  /**
   *
   * @global $msg
   * @return bool
   */
  public function updateFromForm() {
    global $msg;
    $error = false;
    
    // Power Name
    if( empty($_POST['power_name']) ) {
      $msg->add('Name must not be empty.', Message::WARNING);
      $error = true;
    }
    else {
      $this->name = trim($_POST['power_name']);
    }
    
    // Level
    if( empty($_POST['level']) ||
        1 > $_POST['level'] || 30 < $_POST['level'] ) {
      $msg->add('Level must be between 1 and 30.', Message::WARNING);
      $error = true;
    }
    else {
      $this->level = (int)$_POST['level'];
    }
    
    // UseType
    if( empty($_POST['use_type']) || ('at-will' != $_POST['use_type'] &&
        'encounter' != $_POST['use_type'] && 'daily' != $_POST['use_type']) ) {
      $msg->add('Usage Type must be "at-will", "encounter", or "daily."',
        Message::WARNING);
      $error = true;
    }
    else {
      $this->useType = $_POST['use_type'];
    }
    
    $this->target = trim(@$_POST['target']);
    $this->attack = trim(@$_POST['attack']);
    $this->powerRange = trim(@$_POST['powerRange']);
    $this->notes = trim(@$_POST['notes']);
    
    return !$error;    
  }
  
  public function useTypeClass() {
    switch($this->useType) {
      case 'at-will':
        return 'atwill';
        break;
      default:
        return $this->useType;
    }
  }
}
?>
