<?php
/**
 * Start the session and begin output buffering.
 *
 * We use output buffering to allow us to perform header redirects 
 * mid-page processing.
 */
session_start();
ob_start();
?>
