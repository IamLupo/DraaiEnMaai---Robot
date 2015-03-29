<?php

ini_set('display_errors', 1); //DEBUG
set_time_limit(100000);

include_once("include/Page.php");

Page::Initialize();
Page::goHome();
Page::$game->Initialize();
Page::$game->Start();

$failed = 0;
while(Page::$game->CardsFound() < 16) {	
	Page::$game->next = Page::$game->UpdateNext();
	
	//DEBUG
	echo "Next = " . Page::$game->next . "<br />";
	for($j = 0; $j < 4; $j++) {
		for($k = 0; $k < 4; $k++) {
			if(Page::$game->found[($j * 4) + $k])
				echo "1";
			else
				echo "0";
		}
		
		echo " | ";
		
		for($k = 0; $k < 4; $k++) {
			echo Page::$game->memory[($j * 4) + $k];
		}
		
		echo "<br />";
	}

	$result = Page::$game->Click();
	
	echo "<br />";
	
	if(!$result) {	
		$failed++;

		if($failed >= 3) {
			header("Refresh: 0; url=" . $_SERVER['PHP_SELF']);
			break;
		}
	}
	echo "Failed = " . $failed . "<br />";
}

if(Page::$game->CardsFound() == 16)
	Page::$game->End();

Page::Close();

?>