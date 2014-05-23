<?
// http://yellow5.us/journal/server_side_text_linkification/
function LinkifyText($sText, $aAttributes = null, $aProtocols = null, $aSubdomains = null) {
    // Defaults
    $aAttributes = $aAttributes !== null ? $aAttribute : array(
        'rel' => 'nofollow',
        'target' => '_blank',
        'class' => 'linkified',
    );
    $aProtocols = $aProtocols !== null ? $aProtocols : array(
        'http:\/\/',
        'https:\/\/',
        'ftp:\/\/',
        'news:\/\/',
        'nntp:\/\/',
        'telnet:\/\/',
        'irc:\/\/',
        'mms:\/\/',
        'ed2k:\/\/',
        'xmpp:',
        'mailto:'
    );
    $aSubdomains = $aSubdomains !== null ? $aSubdomains : array(
        'www'=>'http://',
        'ftp'=>'ftp://',
        'irc'=>'irc://',
        'jabber'=>'xmpp:'
    );

	$sRELinks = '/(?:(' . implode('|', $aProtocols) . ')[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])|(?:(?:(?:(?:[^@:<>(){}`\'"\/\[\]\s]+:)?[^@:<>(){}`\'"\/\[\]\s]+@)?(' . implode('|', array_keys($aSubdomains)) . ')\.(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6}(?:[\/#?](?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?)|(?:(?:[^@:<>(){}`\'"\/\[\]\s]+@)?((?:(?:(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))(?:\.(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))){3})|(?:[A-Fa-f0-9:]{16,39}))|(?:(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6}))\/(?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s](?:[#?](?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?)?)|(?:[^@:<>(){}`\'"\/\[\]\s]+:[^@:<>(){}`\'"\/\[\]\s]+@((?:(?:(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))(?:\.(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))){3})|(?:[A-Fa-f0-9:]{16,39}))|(?:(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6}))(?:\/(?:(?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?)?(?:[#?](?:[^\^\[\]{}|\\"\'<>`\s]*[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)?))|([^@:<>(){}`\'"\/\[\]\s]+@(?:(?:(?:[^`~!@#$%^&*()_=+\[{\]}\\|;:\'",<.>\/?\s]+\.)+[a-z]{2,6})|(?:(?:(?:(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))(?:\.(?:(?:[0-1]?[0-9]?[0-9])|(?:2[0-4][0-9])|(?:25[0-5]))){3})|(?:[A-Fa-f0-9:]{16,39}))))(?:[^\^*\[\]{}|\\"<>\/`\s]+[^!@\^()\[\]{}|\\:;"\',.?<>`\s])?)/i';

	$sAttributes = '';
	while (list($sKey, $sValue) = each($aAttributes)) {
		$sAttributes .= ' ' . $sKey . '="' . $sValue . '"';
	}

	$sNewText = '';
	while (preg_match($sRELinks, $sText, $aMatches)) {
		$nMatchType = sizeof($aMatches) - 1;
		$sMatchText = $aMatches[$nMatchType];

		$sNewText .= substr($sText, 0, strpos($sText, $aMatches[0]));
		$sText = substr($sText, strpos($sText, $aMatches[0]) + strlen($aMatches[0]));

		if ($nMatchType == 1) {
			$sNewText .= '<a href="' . $aMatches[0] . '"' . $sAttributes . '>' . $aMatches[0] . '</a>';
		} elseif ($nMatchType == 2) {
			$sNewText .= '<a href="' . $aSubdomains[$sMatchText] . $aMatches[0] . '"' . $sAttributes . '>' . $aMatches[0] . '</a>';
		} elseif (($nMatchType == 3) || ($nMatchType == 4)) {
			$sNewText .= '<a href="http://' . $aMatches[0] . '"' . $sAttributes . '>' . $aMatches[0] . '</a>';
		} else {
			$sNewText .= '<a href="mailto:' . $aMatches[0] . '"' . $sAttributes . '>' . $aMatches[0] . '</a>';
		}
	}

	return $sNewText . $sText;
}

function line_as_html($line, $i, $channel) {
    $line = trim($line,"\r\n");
    $line_classes = array();

    $line = htmlspecialchars($line);

    if(preg_match("/^[^\s]+ Action: ([^\s]+)/",$line,$m)) {
        $line = substr($line, 0, 8).substr($line, 16);
        $line_classes[] = 'action';
        $line_classes[] = 'user-'.$m[1];
    }
    if(preg_match("/^[^\s]+ Nick change: ([^\s]+) -&gt; ([^\s]+)$/",$line,$m)) {
        $line = substr($line, 0, 8).substr($line, 21);
        $line_classes[] = 'nickchange';
        $line_classes[] = 'user-'.$m[2];
    } else if(preg_match("/ ([^\s]+) ([^\s]+) joined #{$channel}\.$/",$line,$m)) {
        $line_classes[] = 'join';
        $line_classes[] = 'user-'.$m[1];
    } else if(preg_match("/ mode change /",$line)) {
        $line_classes[] = 'mode';
    } else if(preg_match("/ ([^\s]+) ([^\s]+) left #{$channel}\.$/",$line,$m)) {
        $line_classes[] = 'left';
        $line_classes[] = 'user-'.$m[1];
    } else if(preg_match("/ ([^\s]+) ([^\s]+) left irc: /",$line,$m)) {
        $line_classes[] = 'left';
        $line_classes[] = 'user-'.$m[1];
    } else {
        $line = LinkifyText($line);
        if(preg_match("/^([^\s]+) &lt;([^\s]+)&gt; (.*)$/",$line,$m)) {
            $line_classes[] = 'user-'.$m[2];
            $line = "{$m[1]} <span class='name'>&lt;{$m[2]}&gt;</span> {$m[3]}";
        }
    }

    $line = preg_replace("/^\[([\d]{2}):([\d]{2})\](.*)/", "<span class='ts'>[\\1:\\2]</span><span class='t'>\\3</span>", $line);

    $classes = implode(' ', $line_classes);
    $classes = $classes ? ' class="'.$classes.'"' : '';

    $html = "<li id='{$i}'{$classes}>{$line}</li>";

    return $html;
}
