<? include('header.html.php'); ?>

    <div style="padding: 12px 20px 18px;">
        <h1><?=$title?></h1>
    </div>
    <div class="dates">
    <?
        $maxsize = 0;
        $files = array_reverse(array_slice(scandir($logdir),2));
        foreach($files as $file) {
            $maxsize = max($maxsize, filesize($logdir."/".$file));
        }
        foreach($files as $file) {
            if(strpos($file, $logprefix.'.log') > -1) {
                $w = number_format(filesize($logdir."/".$file)/$maxsize*100,1);
                $filedate = substr($file, strlen($logprefix)+5);
                ?>
        <a href="<?=($baserel.$filedate)?>.html"><?=$filedate?></a>
        <div class='bar'><div style='width:<?=$w?>%'>&nbsp;</div></div>
                <?
            }
        }
    ?>
    </div>

<? include('footer.html.php'); ?>