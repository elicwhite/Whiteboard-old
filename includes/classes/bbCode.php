<?php
/**
 * Copyright Eli White & SaroSoftware 2010
 * 
 * This file is part of WhiteBoard.
 * 
 * WhiteBoard is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * WhiteBoard is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with WhiteBoard.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
/*-----
File: /administration/includes/classes/bbCode.php
Author: SaroSoftware
Date Created: 07/06/2007
Last Edited By: Eli White
Last Edited Date: 3/30/2010
Latest Changes:

Comments:
BBCode Parser

TODO: Nested Quotes

-----*/

class bbCode
{
	/**
	 * Does stuff we need on each bbcode type
	 */
	function startCallback($string)
	{
		$string = trim($string);
		
		return $string;
	}
	
	/**
	 * Easy Stuff
	 */
	function bold($matches)
	{
		$string = $this->startCallback($matches[1]);
		return "<strong>".$string."</strong>";
	}
	function underline($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<span style="text-decoration: underline;">'.$string.'</span>';
	}
	function italic($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<em>'.$string.'</em>';
	}
	function strike($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<del>'.$string.'</del>';
	}
	
	/**
	 * Stylistic 
	 */
	function color($matches)
	{
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);
		return '<span style="color: '.$m2.'">'.$m4.'</span>';
	}
	
	/* This should be changed. We shouldn't be using headers here */
	function size($matches)
	{
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);
		// the largest the number can be is 6
		$m2 = min(6, $m2);
		// the lowest the number can be is 1
		$m2 = max(1, $m2);
		
		return "<h".$m2.">".$m4."</h".$m2.">";
	}
	
	/**
	 * Images and Urls
	 */
	function image($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<img src="'.$string.'" alt="" />';
	}
	
	function urlIn($matches)
	{
		// URLs in between the tag: [url][/url]
		$string = $this->startCallback($matches[1]);
		return '<a href="'.$string.'">'.$string.'</a>';
	}
	function urlOut($matches)
	{
		
		$m2 = $this->startCallback($matches[1]);
		//print_r($matches);
		//echo "<br />";
		$m4 = $this->startCallback($matches[2]);
		
		return '<a href="'.$m2.'">'.$m4.'</a>';
	}
	function emailIn($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<a href="mailto:'.$string.'">'.$string.'</a>';
	}
	function emailOut($matches)
	{
		$m2 = $this->startCallback($matches[1]);
		$m4 = $this->startCallback($matches[2]);
		
		return '<a href="mailto:'.$m2.'">'.$m4.'</a>';
	}
	
	/**
	 * Alignment!
	 */
	function center($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<div style="text-align: center">'.$string.'</div>';
	}
	function left($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<div style="text-align: left">'.$string.'</div>';
	}
	function right($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<div style="text-align: right">'.$string.'</div>';
	}
	
	// Flats a div
	function align($matches)
	{
		$alignment = $this->startCallback($matches[1]);
		
		$content = $this->startCallback($matches[2]);
		$align = "none";

		if (in_array($alignment, array("left", "right")))
			$align = $alignment;
			
		return '<div style="float: '.$align.'">'.$content.'</div>';
	}
	
	function pre($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<pre>'.$string.'</pre>';
	}
	function indent($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<blockquote>'.$string.'</blockquote>';
	}
	
	function codeOut($matches)
	{
		$m2 = $this->startCallback($matches[2]);
		$m4 = $this->startCallback($matches[4]);
		
		return '<pre class="bbcode_code brush:'.$m2.'">'.$m4.'</pre>';
	}
	
	function codeIn($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<pre class="bbcode_code">'.$string.'</pre>';
	}
	
	function icodeOut($matches)
	{
		$m2 = $this->startCallback($matches[2]);
		$m4 = $this->startCallback($matches[4]);
		
		return '<pre class="bbcode_code brush:'.$m2.' light:true">'.$m4.'</pre>';
	}
	
	function icodeIn($matches)
	{
		$string = $this->startCallback($matches[1]);
		return '<pre style="display: inline;" class="bbcode_code brush:plain light:true">'.$string.'</pre>';
	}
	
	
	function parse($string, $return = true)
	{
		
		// No quotes so that this regex still works
		$string = htmlentities($string, ENT_NOQUOTES);
		
		/**
		 * Format the easy stuff, convert [b][u][i][strike]
		 */
		// replace [b]text[/b] with <strong>text</strong>
		$string = preg_replace_callback("/\[b\](.*?)\[\/b\]/is", array($this, 'bold') , $string);
			
		// replace [u]text[/u] with <span style="text-decoration: underline;">text</span>
		$string = preg_replace_callback("/\[u\](.*?)\[\/u\]/is", array($this, 'underline'), $string);
		
		// replace [i]text[/i] with <em>text</em>
		$string = preg_replace_callback("/\[i\](.*?)\[\/i\]/is", array($this, 'italic'), $string);
		
		// replace [strike]text[/strike] with <del>text</del>
		$string = preg_replace_callback("/\[strike\](.*?)\[\/strike\]/is", array($this, 'strike'), $string);
		/**
		 * END FORMATTING EASY STUFF
		 *
		/*
		 * FORMAT COLOR AND SIZE
		 */
		$string = preg_replace_callback('/\[color=\"?(.*?)\"?\](.*?)\[\/color\]/is', array($this, 'color'), $string);
		$string = preg_replace_callback('/\[size=\"?(.*?)\"?\](.*?)\[\/size\]/is', array($this, 'size'),$string);
		/**
		 * END FORMATTING COLOR AND SIZE
		 */
		
		
		/**
		 * Images and urls
		 */
		$string = preg_replace_callback('/\[img\](.*?)\[\/img\]/is', array($this, 'image'),$string);
		
		$string = preg_replace_callback('/\[url\](.*?)\[\/url\]/is', array($this, 'urlIn'),$string);
		$string = preg_replace_callback('/\[url=\"?(.*?)\"?\](.*?)\[\/url\]/is', array($this, 'urlOut'),$string);
		
		/**
		 * Mailto tags
		 */
		$string = preg_replace_callback('/\[email\](.*?)\[\/email\]/is', array($this, 'emailIn'),$string);
		$string = preg_replace_callback('/\[email=\"?(.*?)\"?\](.*?)\[\/email\]/is', array($this, 'emailOut'),$string);
		
		
		/**
		 * Alignment [left][center][middle] 
		 */
		//$string = preg_replace("/\[(left|right|center)\](.*?)\[\/$1\]/is", '<div style="text-align: $1">$2</div>', $string);
		$string = preg_replace_callback("/\[center\](.*?)\[\/center\]/is", array($this, 'center'), $string);
		$string = preg_replace_callback("/\[right\](.*?)\[\/right\]/is", array($this, 'right'), $string);
		$string = preg_replace_callback("/\[left\](.*?)\[\/left\]/is", array($this, 'left'), $string);
		
		$string = preg_replace_callback("/\[align=\"?(.*?)\"?](.*?)\[\/align\]/is", array($this, 'align'), $string);
		
		$string = preg_replace_callback("/\[pre\](.*?)\[\/pre\]/is", array($this, 'pre'), $string);
		
		$string = preg_replace_callback("/\[indent\](.*?)\[\/indent\]/is", array($this, 'indent'), $string);
		
		$string = preg_replace_callback('/\[code\](.*?)\[\/code\]/is', array($this, 'codeIn'),$string);
		$string = preg_replace_callback('/\[code=(&quot;)?(.*?)(&quot;)?\](.*?)\[\/code\]/is', array($this, 'codeOut'),$string);
	
		$string = preg_replace_callback('/\[icode\](.*?)\[\/icode\]/is', array($this, 'icodeIn'),$string);
		$string = preg_replace_callback('/\[icode=(&quot;)?(.*?)(&quot;)?\](.*?)\[\/icode\]/is', array($this, 'icodeOut'),$string);

		/*
		auto format urls
		http://
		https://
		ftp://
		ftps://
		(http|ftp)s?://
		*/
		//$string = preg_replace_callback("/\[indent\](.*?)\[\/indent\]/is", array($this, 'indent'), $string);
		
		$string = nl2br($string);
		$string = str_replace("\t",str_repeat("&nbsp;",4), $string);
		
		
		
		if ($return)
			return $string;
		else
			echo $string;
	}
}

?>