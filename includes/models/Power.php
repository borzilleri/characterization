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
    
    if( empty($_POST['actionType']) || 
        !$this->isValidActionType($_POST['action']) ) {
      $msg->add('Invalid action type.', Message::WARNING);
      $error = true;
    }
    else {
      $this->action = $_POST['actionType'];
    }
    
    $this->target = trim(@$_POST['target']);
    $this->attack = trim(@$_POST['attack']);
    $this->powerRange = trim(@$_POST['powerRange']);
    $this->notes = trim(@$_POST['notes']);
    
    return !$error;    
  }
  
  private function isValidActionType($action) {
    switch($this->action) {
      case 'standard':
      case 'move':
      case 'minor':
      case 'free':
      case 'interrupt':
      case 'reaction':
      case 'none':
        return true;
        break;
      default:
        return false;
        break;
    }
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
  
  public function refresh() {
    $this->used = false;
  }
  
  private function usePower() {
    if( 'at-will' != $this->useType ) {
      $this->used = true;
      return true;
    }
    else {
      return false;
    }
  }
  
  public function togglePower() {
    if( 'at-will' == $this->useType ) {
      $this->used = false;
      return false;
    }
    else {
      $this->used = !$this->used;
      return true;
    }
  }
  
  public function actionTypeDisplay() {
    switch($this->action) {
      case 'move':
        return 'Move Action';
        break;
      case 'minor':
        return 'Minor Action';
        break;
      case 'free':
        return 'Free Action';
        break;
      case 'interrupt':
        return 'Immediate Interrupt';
        break;
      case 'reaction':
        return 'Immediate Reaction';
        break;
      case 'none':
        return 'No Action';
        break;
      case 'standard':
      default:
        return "Standard Action";
        break;
    }
  }
}
?>
