<?php
/**
 *
 */
?>
<div id="Header">
	<div id="MenuBar">
		<ul id="MainMenu">
			<li class="top"><a href="<?=SITE_URL;?>">Characters</a></li>
			<li class="item"><a href="<?=SITE_URL;?>/new">New Character</a></li>
			<li class="item"><a href="<?=SITE_URL;?>/import">Import Character</a></li>
		</ul>
		<? if($char): ?>
		<ul id="PlayerMenu">
			<li class="top"
				><a href="<?=SITE_URL;?>/<?=$char->id;?>"
				>View <?=$char->name;?></a></li>
			<li class="item"
					><a href="<?=SITE_URL;?>/<?=$char->id;?>/edit">Edit</a></li>
			<li class="item"
				><a href="<?=SITE_URL;?>/<?=$char->id?>/power">Powers</a></li>
			<li class="item"
				><a href="<?=SITE_URL;?>/<?=$char->id;?>/delete">Delete</a></li>
		</ul>
		<div class="menu_text" id="PlayerInfo">
			<?=$char->Race->name;?> <?=$char->Archetype->name;?> <?=$char->level;?>
		</div>
		<? endif; ?>
	</div>
</div>

<div id="Content">
	<div id="Messages">
		<div id="clearLink">
			<a href="#" id="ClearMessages" class="ajax">X</a></div>
		<div id="messageText"></div>
	</div>
