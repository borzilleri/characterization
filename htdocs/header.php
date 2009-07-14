<?php
/**
 *
 */
?>
<div id="Header">
  <ul class="inline">
    <li class="firstListItem"><a href="<?=SITE_URL;?>">home</a></li>
  <? if(!empty($_REQUEST['id'])): ?>
    <li><a href="<?=SITE_URL;?>/<?=$_REQUEST['id'];?>">view</a></li>
    <li><a href="<?=SITE_URL;?>/<?=$_REQUEST['id'];?>/edit">edit</a></li>
    <li><a href="<?=SITE_URL;?>/<?=$_REQUEST['id'];?>/power">powers</a></li>
  <? endif; ?>
  </ul>
</div>

<div id="Content">
  <div id="messageBlock"></div>
