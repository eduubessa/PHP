<?php


	require_once('./Database.php');
	
	
	$database = new MySQL();
	$database->table("tbl_accounts")->select()->where("account_user", "LIKE", "eduubessa")->order('teste', 'ASC')->limit(5)->get();