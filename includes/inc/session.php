<?php
/**
 * Begin output buffering. We use output buffering so we can do mid-page
 * redirects using the Location header.
 */
ob_start();

/**
 * Start the session and do any session-related handling.
 * 
 */
session_start();
?>
