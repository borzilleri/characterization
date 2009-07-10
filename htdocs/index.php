<?php
include('autohandler.php');

$chars = Doctrine_Query::create()->from('Player p')->execute();

?>

<ul>
<? foreach($chars as $c): ?>
 <li>
  <a href="<?=SITE_URL;?>/<?=$c->id;?>";?><?=$c->name;?></a> 
  (<?=$c->Race->name;?> <?=$c->Archetype->name;?> <?=$c->level;?>)
 </li>
<? endforeach; ?>
<ul>

<?php include('footer.php'); ?>
