<?php
	require_once( dirname(__FILE__). '/core/dbug.php');

$text = <<<BLOCK
<p>[P:Timestamp|Format=Y]</p>
<p>Nesciunt cliche ''officia''  ennui ethnic iPhone leggings, nisi banjo keytar.  Gentrify nulla  elit Schlitz kale chips shabby chic.  Bicycle rights cred artisan polaroid.</p>
=Erfan's Heading=
[file:file.pdf]

<p>Pug semiotics pour-over, keytar '''Brooklyn''' stumptown artisan Terry Richardson tofu fingerstache.</p>
[file:sample_A.png|Channel=A] [file:sample_B.png|Channel=B|Width=200|Description=Hello Kitty|Height=400]
<p>Cupidatat  veniam keffiyeh chambray culpa.  Pour-over messenger bag Brooklyn thundercats id, sustainable ullamco.  Dreamcatcher meh typewriter sriracha, velit  forage seitan.</p>
[Audio:audio.mp3|Channel=B]
---
[S:Page 5]
Author is [P:Author]
[file:filename.jpg|descriPtion=Lorem ipsum dolor sit amet|Width=300|Class=CLASS|Channel=A]
<p>Nesciunt cliche officia  ennui ethnic '''''iPhone''''' leggings, nisi banjo keytar.  Gentrify nulla  elit Schlitz kale chips shabby chic.  Bicycle rights cred artisan polaroid.</p>
[File:TheBookOfTheDead.pdf|Channel=D|Description=This is Book of the dead]
[Video:video.mp4|Autoplay=Yes|Channel=C]
BLOCK;

// [file:filename.jpg|Description=Lorem ipsum dolor sit amet|Width=300|Class=CLASS|Channel=A]

	preg_match_all("@\[File:((.*?)\.(.*?))\|(.*?)\]@i", $text, $matches);

	if(isset($matches[1]) == TRUE && isset($matches[4]) == TRUE) {
		$i = 0;
		foreach($matches[1] as $file) {
			$file_params = explode("|", $matches[4][$i]);

			$file_width =  '';
			$file_height = '';
			$file_alt = '';
			$file_description = '';

			foreach($file_params as $file_param) {
				unset($matched_param);
				// description
				preg_match("@^Description=(.*?)$@i", $file_param, $matched_param);
				if(isset($matched_param[1]) == TRUE) { $file_description = $matched_param[1]; }
				unset($matched_param);
			}

			$file_html = '';
			$file_html .= '<div class="cms file">';
			$file_html .= '<a href="' . $file . '" ';
			$file_html .= '/>' . $file . '</a>';
			if($file_description != '') { $file_html .= '<p>' . $file_description . '</p>'; }
			$file_html .= '</div>';

			$text = str_ireplace($matches[0][$i], $file_html, $text);
			$i++;
		}
	}
	unset($matches);

	$text = preg_replace("@\[File:(.*?)\]@i", "<div class=\"cms\"><a href=\"$1\">$1</a></div>\n", $text);


	print("<hr/><pre>");
	print $text;

?>
</pre>