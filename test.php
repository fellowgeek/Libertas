<?php
	require_once( dirname(__FILE__). '/core/dbug.php');

$text = <<<BLOCK
Shenhav is a well-known figure in Israel as a public intellectual and as one of the founders of the Mizrahi Democratic Rainbow Coalition, a social movement founded in 1996 by descendants of Jewish refugees, Olim, from Arab countries, which defines itself as an extra-parliamentary movement seeking to challenge the ethnic structure in the Israeli society.
One of the Rainbow Coalition's chief struggles was for lands, in which Shenhav and others petitioned to the Supreme Court of Israel against what he described as an unjust distribution of state owned lands, made worse by decisions of the Israel Land Administration, and won.[1]
* Item1
* Item2
* Item3
* Item4
** Sub-item 1
*** Sub-item
**** Sub-item
** Sub-item 2
* Item5

In late 1996 Shenhav published in Haaretz an article titled "The Bond of Silence",[2] which generated a great deal of reaction. He pointed to an "inter-generational bond of silence between the ideological commissars of the formative years of Zionism ("the salt of the earth") and the contemporary intellectuals of the Israeli Left (also "salt of the earth"). These two generations of Ashkenazi hegemony concur in their silence toward the "Mizrahi problem". He also argued that "denouncing the injustice done to the Palestinians does not endanger the status of our contemporary Ashkenazi intellectuals. It does not endanger their position as a hegemonic cultural group in Israeli society or as an economic class" and that "Dealing with the injustices inflicted on the Palestinians earns them laurels of humanism, the esteemed roles of slaughterers of sacred cows and seekers of peace, the badge of the rebel, and a catharsis in light of the crime of their parents' generation" yet the Palestinian is marked as the "Other", which can be kept on the other side of the fence. The Mizrahi Jews, on the other hand, "cannot be turned into an "other," nor can they be cast beyond the fence; at most, one can construct detours to bypass development towns and poverty neighborhoods". Recognition of the injustices done to the Mizrahim will force the Israeli left to reform itself as well and to relinquish its hegemonic position. To avoid that, they created a taboo.[2]

The article had great resonance in and outside of Israel. It was followed by 25 response article in Haaretz and the multiplicity of references in the media was considered to have marked the beginning of a new public discussion.
Linking the political and intercommunal schism in Identity politics was criticized by conservative intellectuals. It was argued that Mizrahi identity is an anachronism which endangers the Israeli melting pot.[3]
That argument has also been associated with his activities against the Israeli occupation and for a democratic Israel and Palestine. Shenhav said that while the Jews certainly had the right for a collective self-determination in Israel, the state must also reach an agreement with its Palestinian citizens regarding their collective representation as a national minority within it.[4]

BLOCK;

// [file:filename.jpg|Description=Lorem ipsum dolor sit amet|Width=300|Class=CLASS|Channel=A]


	$list_level='';
	$list_level_types =array();
	$stop = false;
	$stop_all = false;

	// parse lists
	function parse_lists_handle($matches,$close=false) {
		global $list_level, $list_level_types, $stop, $stop_all;
		$listtypes=array("*"=>"ul","#"=>"ol");
		$output='';

		if($close==true) {
			$newlevel=0;
		} else {
			$newlevel=strlen($matches[1]);
		}
		while ($list_level!=$newlevel) {
			$listchar=substr($matches[1],-1);
			if(empty($listchar)==false) {
				$listtype=$listtypes[$listchar];
			}
			if ($list_level>$newlevel) {
				$listtype='/'.array_pop($list_level_types);
				$list_level--;
			} else {
				$list_level++;
				array_push($list_level_types,$listtype);
			}
			$output.="<{$listtype}>\n";
		}
		if ($close) return $output;
		$output.="<li>".$matches[2]."</li>\n";
		return $output;
	}

	// parse lists
	function parse_lists_line($line) {
		global $list_level, $list_level_types, $stop, $stop_all;
		$line_regexes=array("list"=>"^([\*\*]+)(.*?)$");
		$stop=false;
		$stop_all=false;
		$called["list"]=false;
		foreach ($line_regexes as $func=>$regex) {
			if (preg_match("/$regex/i",$line,$matches)) {
				$called[$func] = true;
				$line=parse_lists_handle($matches);
				if ($stop || $stop_all) break;
			}
		}
		// if this wasn't a list item, and we are in a list, close the list tag(s)
		if (($list_level>0) && !$called["list"]) $line=parse_lists_handle(false,true ).$line;
		return $line;
	}

	// parse lists
	function parse_lists($in) {
		global $list_level, $list_level_types, $stop, $stop_all;
		$output="";
		$list_level_types=array();
		$list_level=0;
		$lines=explode("\n",$in);
		foreach ($lines as $line) {
			$line=parse_lists_line($line);
			$output.=$line;
		}

		// return output
	    return $output;
	}

	$text = parse_lists($text);

	print("<hr/>");

	//print("<pre>");
	print $text;
	//print("</pre>");

?>