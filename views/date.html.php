<?php
    include('header.html.php');

    $files = array_slice(scandir($logdir),2);
    foreach($files as $i => $file) {
        if($file == $logprefix.'.log.'.$date) {
            $prev = $i > 0 ? substr($files[$i-1], strlen($logprefix)+5) : '';
            $next = $i < count($files)-1 ? substr($files[$i+1], strlen($logprefix)+5) : '';
            break;
        }
    }
?>
    <div class="hdr">
        <h1>
            <a href="/index.html"><?=$title?></a>
            <span class="date"><?=$date?></span>
            <span class="txt">(<a href="/<?=$date?>.txt">txt</a>)</span>
        </h1>
        <ul class="nav">
            <li class="index"><a href='/index.html'>index</a></li>
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
        $i = 0;
        foreach ($lines as $line_num => $line) {
            echo line_as_html($line, $i, $channel);
            $i++;
        }
    ?>
    </ul>
    <ul class="nav" id="urlnav">
        <li class="top"><a href='#0' title="Top">Top</a></li>
        <li class="bottom"><a href='#<?=$i-1?>' title="Bottom">Bottom</a></li>
        <li class="clear"><a href='#none' title="Clear Selection">Clear Selection</a></li>
        <li class="permalink"><a href='#' title="Permalink">Permalink</a></li>
        <? if($network == "irc.freenode.net") { ?>
        <li class="webchat"><a href="http://webchat.freenode.net/?channels=<?=$channel?>" title="Join WebChat" target="_blank">Join WebChat</a>
        <? } ?>
        <li class="github"><a href="http://github.com/KevBurnsJr/irc-log-viewer" title="Fork me on GitHub" target="_blank">Fork me on GitHub</a>
    </ul>

<? include('footer.html.php'); ?>