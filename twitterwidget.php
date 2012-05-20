<?php
/*
Plugin Name: ColorWP Twitter Widget
Plugin URI: http://colorwp.com/plugin/twitter-widget/
Description: Adds a Twitter widget to be used in your theme sidebar. Customizable number of latest tweets and an optional follow button.
Version: 1.0
Author: ColorWP.com
Author URI: http://colorwp.com
License: GNU GPLv2
*/

/*  Copyright 2012 ColorWP.com Twitter Widget Plugin (email : contact@colorwp.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class ColorWP_Twitter_Widget extends WP_Widget
{
  function ColorWP_Twitter_Widget(){
    $widget_ops = array('classname' => 'ColorWP_Twitter_Widget', 'description' => 'Displays a configurable number of tweets from any Twitter username in the sidebar.');
    $this->WP_Widget('ColorWP_Twitter_Widget', 'ColorWP.com Twitter Widget', $widget_ops);
  }
 
  function form($instance){
    $instance = wp_parse_args((array) $instance, array( 'title' => '' ));
	if(!empty($instance['title'])) $title = $instance['title'];
	else $title = '';
    if(!empty($instance['user'])) $username = $instance['user'];
	else $username = '';
	if(!empty($instance['num'])) $num = $instance['num'];
	else $num = '';
	if(!empty($instance['followbutton'])) $followbutton = $instance['followbutton'];
	else $followbutton = true;
    ?>
    <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
    <p><label for="<?php echo $this->get_field_id('user'); ?>">Twitter Username: <input class="widefat" id="<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>" type="text" value="<?php echo esc_attr($username); ?>" /></label></p>
    <p><label for="<?php echo $this->get_field_id('num'); ?>">Number of tweets to display:
            <select name="<?php echo $this->get_field_name('num'); ?>" class="widefat">
                <option value="1" name="1">1</option>
                <option value="2" name="1">2</option>
                <option value="3" name="1">3</option>
                <option value="4" name="1">4</option>
                <option value="5" name="1" selected>5</option>
                <option value="6" name="1">6</option>
                <option value="7" name="1">7</option>
                <option value="8" name="1">8</option>
                <option value="9" name="1">9</option>
                <option value="10" name="1">10</option>
                <option value="20" name="1">20</option>
                <option value="30" name="1">30</option>
                <option value="0" name="1">Unlimited</option>
            </select>
    </p>
    <p><label for="<?php echo $this->get_field_id('align'); ?>">Align text to:
            <select name="<?php echo $this->get_field_name('align'); ?>" class="widefat">
                <option value="left" name="1" selected>Left</option>
                <option value="center" name="1">Center</option>
                <option value="right" name="1">Right</option>
                <option value="justify" name="1">Justify</option>
            </select>
    </p>
    <p><input type="checkbox" name="<?php echo $this->get_field_name('followbutton'); ?>" value="1" <?php echo ($followbutton?'checked':'') ?>> Show follow button below tweets</p>
    <?php
  }
 
  function update($new_instance, $old_instance){
    $instance                   = $old_instance;
    $instance['title']          = $new_instance['title'];
    $instance['user']           = $new_instance['user'];
    $instance['num']            = $new_instance['num'];
    $instance['align']          = $new_instance['align'];
    $instance['followbutton']   = $new_instance['followbutton'];
    return $instance;
  }
 
  function widget($args, $instance){
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
 
    echo $before_title;
    if (!empty($title)) echo $title;
    else echo 'Twitter';
    echo $after_title;
    
    if(isset($instance['user'])){
        $rss = new lastRSS;
        if(isset($instance['num']) && is_numeric($instance['num'])) $rss->items_limit = $instance['num'];
        // load some RSS file
        $rs = $rss->get('http://api.twitter.com/1/statuses/user_timeline.rss?screen_name='.$instance['user']);
        if ($rs) {
            if($instance['align']=='center') $align = 'center';
            elseif($instance['align']=='right') $align = 'right';
            elseif($instance['align']=='justify') $align = 'justify';
            else $align = 'left';
                
            echo '<span style="text-align:'.$align.'">';
            foreach($rs['items'] as $item){
                $body = trim(str_ireplace($instance['user'].':', '', $item['title']));
                echo '<p>'.$body.'</p>';
            }
            echo ($instance['followbutton'])?'<a href="https://twitter.com/'.$instance['user'].'" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @JonyIsMyName</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>':'';
            echo '</span>';
        }
        else {
        echo "Can't access Twitter.";
        }
    }
 
    echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("ColorWP_Twitter_Widget");') );


/** 
* lastRSS 
* Simple yet powerfull PHP class to parse RSS files. 
*/ 
class lastRSS { 
    // ------------------------------------------------------------------- 
    // Public properties 
    // ------------------------------------------------------------------- 
    var $default_cp = 'UTF-8'; 
    var $CDATA = 'nochange'; 
    var $cp = ''; 
    var $items_limit = 0; 
    var $stripHTML = False; 
    var $date_format = ''; 

    // ------------------------------------------------------------------- 
    // Private variables 
    // ------------------------------------------------------------------- 
    var $channeltags = array ('title', 'link', 'description', 'language', 'copyright', 'managingEditor', 'webMaster', 'lastBuildDate', 'rating', 'docs');
    var $itemtags = array('title', 'link', 'description', 'author', 'category', 'comments', 'enclosure', 'guid', 'pubDate', 'source');
    var $imagetags = array('title', 'url', 'link', 'width', 'height'); 
    var $textinputtags = array('title', 'description', 'name', 'link'); 

    // ------------------------------------------------------------------- 
    // Parse RSS file and returns associative array. 
    // ------------------------------------------------------------------- 
    function Get ($rss_url) { 
            $result = $this->Parse($rss_url); 
            if ($result) $result['cached'] = 0; 
            return $result; 
    } 
     
    // ------------------------------------------------------------------- 
    // Modification of preg_match(); return trimed field with index 1 
    // from 'classic' preg_match() array output 
    // ------------------------------------------------------------------- 
    function my_preg_match ($pattern, $subject) { 
        // start regullar expression 
        preg_match($pattern, $subject, $out); 

        // if there is some result... process it and return it 
        if(isset($out[1])) { 
            // Process CDATA (if present) 
            if ($this->CDATA == 'content') { // Get CDATA content (without CDATA tag) 
                $out[1] = strtr($out[1], array('<![CDATA['=>'', ']]>'=>'')); 
            } elseif ($this->CDATA == 'strip') { // Strip CDATA 
                $out[1] = strtr($out[1], array('<![CDATA['=>'', ']]>'=>'')); 
            } 

            // If code page is set convert character encoding to required 
            if ($this->cp != '') 
                //$out[1] = $this->MyConvertEncoding($this->rsscp, $this->cp, $out[1]); 
                $out[1] = iconv($this->rsscp, $this->cp.'//TRANSLIT', $out[1]); 
            // Return result 
            return trim($out[1]); 
        } else { 
        // if there is NO result, return empty string 
            return ''; 
        } 
    } 

    // ------------------------------------------------------------------- 
    // Replace HTML entities &something; by real characters 
    // ------------------------------------------------------------------- 
    function unhtmlentities ($string) { 
        // Get HTML entities table 
        $trans_tbl = get_html_translation_table (HTML_ENTITIES, ENT_QUOTES); 
        // Flip keys<==>values 
        $trans_tbl = array_flip ($trans_tbl); 
        // Add support for &apos; entity (missing in HTML_ENTITIES) 
        $trans_tbl += array('&apos;' => "'"); 
        // Replace entities by values 
        return strtr ($string, $trans_tbl); 
    } 

    // ------------------------------------------------------------------- 
    // Parse() is private method used by Get() to load and parse RSS file. 
    // Don't use Parse() in your scripts - use Get($rss_file) instead. 
    // ------------------------------------------------------------------- 
    function Parse ($rss_url) { 
        // Open and load RSS file 
        if ($f = @fopen($rss_url, 'r')) { 
            $rss_content = ''; 
            while (!feof($f)) { 
                $rss_content .= fgets($f, 4096); 
            } 
            fclose($f); 

            // Parse document encoding 
            $result['encoding'] = $this->my_preg_match("'encoding=[\'\"](.*?)[\'\"]'si", $rss_content); 
            // if document codepage is specified, use it 
            if ($result['encoding'] != '') 
                { $this->rsscp = $result['encoding']; } // This is used in my_preg_match() 
            // otherwise use the default codepage 
            else 
                { $this->rsscp = $this->default_cp; } // This is used in my_preg_match() 

            // Parse CHANNEL info 
            preg_match("'<channel.*?>(.*?)</channel>'si", $rss_content, $out_channel); 
            foreach($this->channeltags as $channeltag) 
            { 
                $temp = $this->my_preg_match("'<$channeltag.*?>(.*?)</$channeltag>'si", $out_channel[1]); 
                if ($temp != '') $result[$channeltag] = $temp; // Set only if not empty 
            } 
            // If date_format is specified and lastBuildDate is valid 
            if ($this->date_format != '' && ($timestamp = strtotime($result['lastBuildDate'])) !==-1) { 
                        // convert lastBuildDate to specified date format 
                        $result['lastBuildDate'] = date($this->date_format, $timestamp); 
            } 

            // Parse TEXTINPUT info 
            preg_match("'<textinput(|[^>]*[^/])>(.*?)</textinput>'si", $rss_content, $out_textinfo); 
                // This a little strange regexp means: 
                // Look for tag <textinput> with or without any attributes, but skip truncated version <textinput /> (it's not beggining tag)
            if (isset($out_textinfo[2])) { 
                foreach($this->textinputtags as $textinputtag) { 
                    $temp = $this->my_preg_match("'<$textinputtag.*?>(.*?)</$textinputtag>'si", $out_textinfo[2]); 
                    if ($temp != '') $result['textinput_'.$textinputtag] = $temp; // Set only if not empty 
                } 
            } 
            // Parse IMAGE info 
            preg_match("'<image.*?>(.*?)</image>'si", $rss_content, $out_imageinfo); 
            if (isset($out_imageinfo[1])) { 
                foreach($this->imagetags as $imagetag) { 
                    $temp = $this->my_preg_match("'<$imagetag.*?>(.*?)</$imagetag>'si", $out_imageinfo[1]); 
                    if ($temp != '') $result['image_'.$imagetag] = $temp; // Set only if not empty 
                } 
            } 
            // Parse ITEMS 
            preg_match_all("'<item(| .*?)>(.*?)</item>'si", $rss_content, $items); 
            $rss_items = $items[2]; 
            $i = 0; 
            $result['items'] = array(); // create array even if there are no items 
            foreach($rss_items as $rss_item) { 
                // If number of items is lower then limit: Parse one item 
                if ($i < $this->items_limit || $this->items_limit == 0) { 
                    foreach($this->itemtags as $itemtag) { 
                        $temp = $this->my_preg_match("'<$itemtag.*?>(.*?)</$itemtag>'si", $rss_item); 
                        if ($temp != '') $result['items'][$i][$itemtag] = $temp; // Set only if not empty 
                    } 
                    // Strip HTML tags and other bullshit from DESCRIPTION 
                    if ($this->stripHTML && $result['items'][$i]['description']) 
                        $result['items'][$i]['description'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['description']))); 
                    // Strip HTML tags and other bullshit from TITLE 
                    if ($this->stripHTML && $result['items'][$i]['title']) 
                        $result['items'][$i]['title'] = strip_tags($this->unhtmlentities(strip_tags($result['items'][$i]['title']))); 
                    // If date_format is specified and pubDate is valid 
                    if ($this->date_format != '' && ($timestamp = strtotime($result['items'][$i]['pubDate'])) !==-1) { 
                        // convert pubDate to specified date format 
                        $result['items'][$i]['pubDate'] = date($this->date_format, $timestamp); 
                    } 
                    // Item counter 
                    $i++; 
                } 
            } 

            $result['items_count'] = $i; 
            return $result; 
        } 
        else // Error in opening return False. Probably fopen() is not supported by the server
        { 
            return False; 
        } 
    } 
} 

?>