<?php

require_once('functions.php');

$title = "#Rest on Freenode";
$subtitle = "";

if(getenv('APP_ENV') == 'local') {
    $logdir = dirname(__FILE__)."/sample-logs";
    $baseurl = "http://rest.local";
    $logprefix = "rest";
    $channel_name = "#rest";
} else {
    $logdir = "/home/kevburns/eggdrop/logs/rest";
    $baseurl = "http://rest.hackyhack.net";
    $logprefix = "rest";
    $channel_name = "#rest";
}
if(isset($_REQUEST['date'])) {
    $logdate = $_REQUEST['date'];
    $filename = $logdir.'/'.$logprefix.'.log.'.$logdate;
    $lines = file_exists($filename) ? file($filename) : null;
    $subtitle = " - ".date('D, M jS Y ',strtotime($logdate));
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title><?=$title.$subtitle?></title>
        <link rel="stylesheet" type="text/css" href="/css/default.css" media="screen" />
        <script type="text/javascript" src="/js/jquery-1.4.min.js"></script>
        <script type="text/javascript" src="/js/jquery.plugins.js"></script>
        <script type="text/javascript" src="/js/global.js"></script>
    </head>
    <body><a name="top"></a><div class="wrapper">
<?
	if(isset($_REQUEST['date']) && count($lines)) { 
		$files = array_slice(scandir($logdir),2);
		foreach($files as $i => $file) {
            if($file == $logprefix.'.log.'.$logdate) {
                $prev = $i > 0 ? substr($files[$i-1], strlen($logprefix)+5) : '';
                $next = $i < count($files)-1 ? substr($files[$i+1], strlen($logprefix)+5) : '';
                break;
            }
        }
    ?>
        <div class="hdr">
            <h1><?=$title?> <span class="date"><?=date('D, M jS Y ',strtotime($logdate))?></span></h1>
            <ul class="nav">
                <li class="index"><a href='/'>index</a></li>
            <? if($prev) { ?>
                <li class="prev"><a href='<?=$baseurl?>/<?=$prev?>.html'>prev</a></li>
            <? } else { ?>
                <li class="prev"><span>prev</span></li>
            <? } ?>
            <? if($next) { ?>
                <li class="next"><a href='<?=$baseurl?>/<?=$next?>.html'>next</a></li>
            <? } else { ?>
                <li class="next"><span>next</span></li>
            <? } ?>
            </ul>
        </div>
        <ul class="lines">
        <? 
        $times = array();
        $i = 0;
        foreach ($lines as $line_num => $line) { 
            $line = trim($line,"\r\n");
            $line_classes = array();
            
            $line = htmlspecialchars($line);
            
            if(preg_match("/^Action: /",$line)) {
                $line = preg_replace("/^Action: /","",$line);
                $line_classes[] = 'action';
            }
            if(preg_match("/^Nick change: /",$line)) {
                $line_classes[] = 'nickchange';
            } else if(preg_match("/ joined $channel_name\.$/",$line)) {
                $line_classes[] = 'join';
            } else if(preg_match("/ mode change /",$line)) {
                $line_classes[] = 'mode';
            } else if(preg_match("/ left $channel_name\.$/",$line)) {
                $line_classes[] = 'left';
            } else if(preg_match("/ left irc: /",$line)) {
                $line_classes[] = 'left';
            } else if(preg_match("/left irc: Quit: /",$line)) {
                $line_classes[] = 'left';
                $line_classes[] = 'quit';
            } else if(preg_match("/left irc: Read error: /",$line)) {
                $line_classes[] = 'left';
                $line_classes[] = 'error';
            } else {
                $line = LinkifyText($line);
            }
            
            $line = preg_replace("/^\[([\d]{2}):([\d]{2})\](.*)/", "<a href='#l$i' class='ts'>[\\1:\\2]</a><span class='t'>\\3</span>", $line);
            
            $classes = implode(' ', $line_classes);
            $classes = $classes ? ' class="'.$classes.'"' : '';
            $line_item = "";
        ?>
            <li id='<?=$i?>'<?=$classes?>><?=$line?></li>
        <? 
            $i++;
        } 
        ?> 
        </ul>
        <ul class="nav" id="urlnav">
            <li class="top"><a href='#top' title="Top">Top</a></li>
            <li class="bottom"><a href='#bottom' title="Bottom">Bottom</a></li>
            <li class="clear"><a href='#none' title="Clear Selection">Clear Selection</a></li>
            <li class="permalink"><a href='#' title="Permalink">Permalink</a></li>
        </ul>
<?
	} else {
		$files = scandir($logdir);
		foreach($files as $file) {
			if(strpos($file, $logprefix.'.log') > -1) {
				$filedate = substr($file, strlen($logprefix)+5);
				echo "<a href='".$baseurl."/".$filedate.".html'>".$filedate."</a><br />";
			}
		}
	}
?>
    </div><a name="bottom"></a></body>
</html>