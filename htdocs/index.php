<?php
include('autohandler.php');

$char_list = Doctrine_Query::create()->from('Player p')->execute();
?>

<ul>
  <? foreach($char_list as $c): ?>
  <li><a href="<?=SITE_URL;?>/<?=$c->id;?>";?><?=$c->name;?></a> 
    (<?=$c->Race->name;?> <?=$c->Archetype->name;?> <?=$c->level;?>)
  </li>
  <? endforeach; ?>
</ul>

<?php include('footer.php'); ?>