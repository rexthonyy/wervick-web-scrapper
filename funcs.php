<?php
	include_once "database/DB.const.php";
	include_once "database/Table.const.php";
	include_once "database/Column.const.php";
	include_once "database/Database.cls.php";
	include_once "database/DbTable.cls.php";
	include_once "database/DbTableQuery.cls.php";
	include_once "database/DbTableOperator.cls.php";
		
	include_once('helpers/simple_html_dom.php');
	
	function getJobListFromHTML($html){
		$postList = array();
		exit;
		for($i = 0; ; $i++){
			$title = $html->getElementsByTagName('h3', $i);
			$time = $html->getElementsByTagName('time', $i);
			if(isset($title)){
				$content = $title->nextSibling();
				$link = $title->parentNode();
				$img = $link->parentNode()->parentNode()->first_child()->first_child();
				
				$post = array();
				$post['title'] = $title->innertext;
				$post['content'] = $content->innertext;
				$post['link'] = "https://www.tipranks.com".$link->href;
				$post['pic'] = $img->src;
				$post['time'] = $time->innertext;
				
				$postList[] = $post;
			}else{
				break;
			}
		}
		
		return $postList;
	}
?>