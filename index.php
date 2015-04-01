<?php

ini_set('display_errors', 1); //DEBUG
set_time_limit(100000);

include_once("include/Page.php");

Page::Initialize();
Page::goHome();
Page::$game->Initialize("help0001%40gmail.com", "NL", "Peter", "v.d.Laan");
Page::$game->Start();
Page::$game->Run();

if(Page::$game->isFinished())
	Page::$game->End();

Page::Close();

?>