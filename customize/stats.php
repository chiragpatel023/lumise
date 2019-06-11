<?php
require('autoload.php');
global $lumise, $lumise_helper;
$resources = $lumise->lib->recent_resources();
$content = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0">';

foreach($resources as $resource => $items){
    $content .= '
    <'.$resource.'>';
    
    foreach ($items as $item) {
        $content .= '
        <item>
			<title>'.$item['name'].'</title>
			<thumb>'.(isset($item['thumbnail_url'])? $item['thumbnail_url'] : 'http://via.placeholder.com/245x185').'</thumb>
			<cate>'.implode(', ', $item['categories']).'</cate>
		</item>';
    }
    
    $content .= '
    </'.$resource.'>';
}

$content .= '
</rss>';

file_put_contents(dirname(__FILE__) . DS .'stats.rss.xml', $content);
echo 'Done';
