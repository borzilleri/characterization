<?php
/**
 * @author Jonathan Borzilleri
 */

/**
 * Message
 *
 * Stores messages for the user in the user's session. Because of the fact that
 * we clear sessions after login and logout, messages need to be added after
 * calling UserLoginTable::login and UserLoginTable::logout if you want the
 * user to receive them.
 *
 */
class Message {
    const SUCCESS = 0;
    const NOTICE = 1;
    const WARNING = 2;
    const ERROR = 3;
    
    /**
     * @static
     * @var array String representations of the messaging levels.
     */
    public static $strings = array(
        self::NOTICE => 'Notice',
        self::WARNING => 'Warning',
        self::ERROR => 'Error',
        self::SUCCESS => 'Success'
    );

    /**
     * add
     *
     * Adds a message to the list of messages that should be displayed to the
     * user on the next page view. Make sure you pass the string to gettext()
     * first and pass its result to this method.
     *
     */
    public function add($message, $level = self::NOTICE) {
        $this->setupSession();
        
        $_SESSION['messages'][] = array(
          'level' => $level,
          'message' => _(self::$strings[$level]).": ".$message
        );
    }
  
    public function messages() {
        $this->setupSession();        
        return $_SESSION['messages'];
    }
    
    public function clear() {
        $_SESSION['messages'] = array();
    }
    
    private function setupSession() {
        if ( !isset($_SESSION['messages']) || !is_array($_SESSION['messages']) )
            $_SESSION['messages'] = array();
    }
}

?>
