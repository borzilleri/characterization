<?php
/**
 * Handles end-of-page cleanup.
 */ 
$etime = microtime(true);
?>
  </div><!-- end div#Content -->
  <div id="Footer">
   <div id="CopyrightInfo">
    <span class="copyright">Copyright &copy;</span>
    <span class="copyrightDate">2009 Jonathan Borzilleri</span>
   </div>
   <?=$config['dev']['debug']?'<div class="debugTimer">'._('Elasped Time: ').($etime-$stime).'</div>':'';?>
  </div><!-- end div#Footer -->
 </body>
</html>
