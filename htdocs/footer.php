<?php
/**
 * Handles end-of-page cleanup.
 */ 
$etime = microtime(true);
?>
  </div><!-- end div#Content -->
  <div id="Footer" class="clear">
   <div id="CopyrightInfo">
    <span class="copyright">Copyright &copy;</span>
    <span class="copyrightDate">2009 Jonathan Borzilleri</span>
   </div>
   <div>
     <a href="http://validator.w3.org/check?uri=referer">xhtml 1.0</a>
   </div>
   <?=$config['dev']['debug']?'<div class="debugTimer">'._('Elasped Time: ').($etime-$stime).'</div>':'';?>
  </div><!-- end div#Footer -->
 </body>
</html>
