<?php
/**
 *
 */
?>
<div id="Header">
  <div id="MenuBar">
    <ul id="MainMenu">
      <li class="top"><a href="<?=SITE_URL;?>">character list</a></li>
      <li class="item"><a href="<?=SITE_URL;?>/new">new character</a></li>
      <li class="item"><a href="<?=SITE_URL;?>/import">import character</a></li>
    </ul>
    <? if($char): ?>
    <ul id="PlayerMenu">
      <li class="top"
        ><a href="<?=SITE_URL;?>/<?=$char->id;?>">view</a></li>
      <li class="item"
        ><a href="<?=SITE_URL;?>/<?=$char->id;?>/edit">edit</a></li>
      <li class="item"
        ><a href="<?=SITE_URL;?>/<?=$char->id?>/power">powers</a></li>
      <li class="item"
        ><a href="<?=SITE_URL;?>/<?=$char->id;?>/delete">delete</a></li>
    </ul>
    <? endif; ?>
  </div>
</div>

<div id="Content">
  <div id="messageBlock"></div>
