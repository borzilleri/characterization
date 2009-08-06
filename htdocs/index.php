<?php
include('autohandler.php');

$char_list = Doctrine_Query::create()
  ->from('Player p')->orderBy('p.name')->execute();

$i = 0;
?>
<ul class="list">
<? foreach($char_list as $c): ?>
  <li class="row<?=$i;$i=($i+1)%2;?>">
    <div class="listLinks">
      [<a href="<?=SITE_URL;?>/<?=$c->id;?>">view</a>&nbsp;|
      <a href="<?=SITE_URL;?>/<?=$c->id;?>/edit">edit</a>]
    </div>
    <div class="listLabel">
      <?=$c->name;?>
    </div>
    <div class="listInfo">
      <?=$c->Race->name;?> <?=$c->Archetype->name;?> <?=$c->level;?>
    </div>
  </li>
<? endforeach; ?>
</ul>

<?php include('footer.php'); ?>