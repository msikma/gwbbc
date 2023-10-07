<?php
/**
 * GW BBC
 *
 * @author    msikma https://michielsikma.com/
 * @license   MIT https://opensource.org/license/mit/
 * @mod       GW BBC
 */

require_once("$sourcedir/gwbbc.util.php");

if (!defined('SMF')) {
  exit;
}

class GWBBC {
	public static function addCodes(&$codes) {
    global $txt;
    
    loadLanguage('gwbbc');
  
    // [youtube] code.
    $bbc_youtube = array(
      'tag' => 'youtube',
      'type' => 'unparsed_content',
      'validate' => function(&$tag, &$data, $disabled) {
        // If the user passed a youtube.com or youtu.be link, extract the ID.
        if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $data, $matches)) {
          $data = $matches[1];
        }
      },
      'content' => '<div class="gwbbc gwbbc_youtube" data-content="$1"><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/$1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe></div>',
      'block_level' => true,
    );

    // [hide] code.
    $bbc_hide_decorator = '<script>GWBBC.decorateHide(document.currentScript)</script>';
    $bbc_hide = array(
      'tag' => 'hide',
      'type' => 'unparsed_content',
      'content' => '<div class="gwbbc gwbbc_hide is_hidden"><a href="#" class="hide_title"><span class="reason">'.$txt['gwbbc_hide_default'].'</span> <span class="reveal">'.$txt['gwbbc_hide_click_to_reveal'].'</span></a><div class="hide_content"><div class="hide_content_inner">$1</div></div></div>'.$bbc_hide_decorator,
      'block_level' => true,
    );
    $bbc_hide_pe = array(
      'tag' => 'hide',
      'type' => 'parsed_equals',
      'before' => '<div class="gwbbc gwbbc_hide is_hidden"><a href="#" class="hide_title"><span class="reason">$1</span> <span class="reveal">'.$txt['gwbbc_hide_click_to_reveal'].'</span></a><div class="hide_content"><div class="hide_content_inner">',
      'after' => '</div></div></div>'.$bbc_hide_decorator,
      'quoted' => 'optional',
      'parsed_tags_allowed' => array('url', 'iurl', 'ftp', 'b', 'i', 'u'),
      'block_level' => true,
    );

    // [irc] code.
    $bbc_irc = array(
      'tag' => 'irc',
      'type' => 'parsed_equals',
      'before' => '<a href="$1" class="gwbbc gwbbc_irc">',
      'after' => '</a>',
      'quoted' => 'optional',
      'parsed_tags_allowed' => array('img', 'b', 'i', 'u'),
      'block_level' => false,
    );

    // [spoiler] code.
    $bbc_spoiler_decorator = '<script>GWBBC.decorateSpoiler(document.currentScript)</script>';
    $bbc_spoiler = array(
      'tag' => 'spoiler',
      'type' => 'unparsed_content',
      'content' => '<span class="gwbbc gwbbc_spoiler is_hidden" title="'.$txt['gwbbc_spoiler'].'">$1</span>'.$bbc_spoiler_decorator,
      'block_level' => false,
    );

    // [dohtml] code.
    $bbc_dohtml = array(
      'tag' => 'dohtml',
      'type' => 'unparsed_content',
      'content' => '<div class="gwbbc gwbbc_dohtml">$1</div>',
      'validate' => function(&$tag, &$data, $disabled) {
        if ($disabled['dohtml']) {
          return;
        }
        $data = sanitize_html(htmlspecialchars_decode($data));
      },
      'block_level' => true,
    );
    
    // [quote] with author and date, but no link.
    // Note: mostly copied from Subs.php. Used for very old converted quotes (around 2005).
    // Example: [quote date=1537087368 author=Frisky SKeleton]
    $bbc_quote_no_link = array(
      'tag' => 'quote',
      'parameters' => array(
        'author' => array('match' => '([^<>]{1,192}?)'),
        'date' => array('match' => '(\d+)', 'validate' => 'timeformat'),
      ),
      'before' => '<div class="quoteheader"><div class="topslice_quote">'.$txt['quote_from'].': {author} '.$txt['search_on'].' {date}</div></div><blockquote>',
      'after' => '</blockquote><div class="quotefooter"><div class="botslice_quote"></div></div>',
      'block_level' => true,
    );
  
    $codes[] = $bbc_youtube;
    $codes[] = $bbc_irc;
    $codes[] = $bbc_hide;
    $codes[] = $bbc_hide_pe;
    $codes[] = $bbc_spoiler;
    $codes[] = $bbc_dohtml;
    $codes[] = $bbc_quote_no_link;
  }

	public static function addButtons(&$bbc_tags) {
    global $txt;
    
    loadLanguage('gwbbc');
  
    $tag_youtube = array(
      'image' => 'gwbbc.youtube',
      'code' => 'youtube',
      'before' => '[youtube]',
      'after' => '[/youtube]',
      'description' => $txt['gwbbc_youtube_description'],
    );
  
    $tag_hide = array(
      'image' => 'gwbbc.hide',
      'code' => 'hide',
      'before' => '[hide]',
      'after' => '[/hide]',
      'description' => $txt['gwbbc_hide_description'],
    );
  
    $tag_irc = array(
      'image' => 'gwbbc.irc',
      'code' => 'irc',
      'before' => '[irc]',
      'after' => '[/irc]',
      'description' => $txt['gwbbc_irc_description'],
    );
  
    $tag_spoiler = array(
      'image' => 'gwbbc.spoiler',
      'code' => 'spoiler',
      'before' => '[spoiler]',
      'after' => '[/spoiler]',
      'description' => $txt['gwbbc_spoiler_description'],
    );
  
    $tag_dohtml = array(
      'image' => 'gwbbc.dohtml',
      'code' => 'dohtml',
      'before' => '[dohtml]',
      'after' => '[/dohtml]',
      'description' => $txt['gwbbc_dohtml_description'],
    );
    
    insert_bbc_button_after($bbc_tags, $tag_youtube, 'code', true);
    insert_bbc_button_after($bbc_tags, $tag_hide, 'youtube', false);
    insert_bbc_button_after($bbc_tags, $tag_spoiler, 'hide', false);
    insert_bbc_button_after($bbc_tags, $tag_dohtml, 'spoiler', false, true);
	}

	public static function addStyles() {
		global $context, $boardurl;
		$context['html_headers'] .= '<link rel="stylesheet" type="text/css" href="'.$boardurl.'/Sources/gwbbc.style.css">';
		$context['html_headers'] .= '<script type="text/javascript" src="'.$boardurl.'/Sources/gwbbc.script.js"></script>';
  }
}
