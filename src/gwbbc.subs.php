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
  
    // YouTube embed code.
    //
    // [youtube]https://www.youtube.com/watch?v=xcjRiJWjvVY[/youtube]
    // [youtube]https://youtu.be/xcjRiJWjvVY[/youtube]
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

    // Post content section that is hidden and expandable.
    //
    // [hide]My [b]post content[/b] here.[/hide]
    $bbc_hide_decorator = '<script>GWBBC.decorateHide(document.currentScript)</script>';
    $bbc_hide = array(
      'tag' => 'hide',
      'before' => '<div class="gwbbc gwbbc_hide is_hidden"><a href="#" class="hide_title"><span class="reason">'.$txt['gwbbc_hide_default'].'</span> <span class="reveal">'.$txt['gwbbc_hide_click_to_reveal'].'</span></a><div class="hide_content"><div class="hide_content_inner">',
      'after' => '</div></div></div>'.$bbc_hide_decorator,
      'block_level' => true,
    );
    // [hide=Changelog]Changelog [b]content[/b] here.[/hide]
    // [hide="Changelog"]Changelog [b]content[/b] here.[/hide]
    $bbc_hide_pe = array(
      'tag' => 'hide',
      'type' => 'parsed_equals',
      'before' => '<div class="gwbbc gwbbc_hide is_hidden"><a href="#" class="hide_title"><span class="reason">$1</span> <span class="reveal">'.$txt['gwbbc_hide_click_to_reveal'].'</span></a><div class="hide_content"><div class="hide_content_inner">',
      'after' => '</div></div></div>'.$bbc_hide_decorator,
      'quoted' => 'optional',
      'block_level' => true,
    );

    // IRC server link.
    //
    // [irc=irc://irc.whahay.net/targ_like_cupcakes]targ irc channel[/irc]
    $bbc_irc = array(
      'tag' => 'irc',
      'type' => 'parsed_equals',
      'before' => '<a href="$1" class="gwbbc gwbbc_irc">',
      'after' => '</a>',
      'quoted' => 'optional',
      'block_level' => false,
    );

    // Spoiler tag.
    //
    // [spoiler]All this content is hidden until clicked[/spoiler]
    $bbc_spoiler_decorator = '<script>GWBBC.decorateSpoiler(document.currentScript)</script>';
    $bbc_spoiler = array(
      'tag' => 'spoiler',
      'type' => 'unparsed_content',
      'content' => '<span class="gwbbc gwbbc_spoiler is_hidden" title="'.$txt['gwbbc_spoiler'].'">$1</span>'.$bbc_spoiler_decorator,
      'block_level' => false,
    );

    // HTML embed code.
    //
    // All content is sanitized.
    //
    // [dohtml]<b>HTML code goes here.</b> All content is sanitized, so this doesn't work: </div>[/dohtml]
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
    
    // Quote tag with author and date, but no link.
    //
    // Mostly copied from Subs.php and modified. Used for very old converted quotes (around 2005).
    //
    // [quote date=1537087368 author=Frisky SKeleton]Quoted content goes here.[/quote]
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

    // Updated size tag with three digit support.
    //
    // This is used for very old converted posts that permitted larger size values.
    // Mostly copied from Subs.php and modified.
    //
    // [size=200pt]Really, really large text.[/size]
    $bbc_size = array(
      'tag' => 'size',
      'type' => 'unparsed_equals',
      'test' => '([1-9][\d]?[\d]?p[xt]|small(?:er)?|large[r]?|x[x]?-(?:small|large)|medium|(0\.[1-9]|[1-9](\.[\d][\d]?)?)?em)\]',
      'before' => '<span style="font-size: $1;" class="bbc_size">',
      'after' => '</span>',
    );

    // The following size tag catches all other uses from old posts.
    // Throughout the years various alternate argument styles were valid.
    // This sanitizes the values to counter abuse.
    // Examples: [size=+1], [size=-1], [size=0], [size=1000000], [size=45], [size=50%], [size=2000pt].
    $bbc_size_catchall = array(
      'tag' => 'size',
      'type' => 'unparsed_equals',
      'before' => '<span style="font-size: $1;" class="bbc_size">',
      'after' => '</span>',
      'validate' => function(&$tag, &$data, $disabled) {
        $data = trim($data);
        
        // Quick sanity check: if there's no valid value, just display 100% size.
        if ($data !== '0' && empty($data)) {
          $data = '100%';
          return;
        }
        
        // The base size in pt: used to calculate relative font sizes like +1.
        $base_size = 10;
        
        // Minimum and maximum sizes for various supported units.
        $unit_minmax = [
          'px' => [1, 200],
          'pt' => [2, 150],
          'em' => [0.1, 10],
          'rem' => [0.1, 10],
          '%' => [5, 1000]
        ];
        
        // Static em sizes for font sizes without unit under 10.
        $static_sizes = [
          // 1-7 are from SMF, and are covered by its regular size tag.
          1 => 0.7,
          2 => 1.0,
          3 => 1.35,
          4 => 1.45,
          5 => 2.0,
          6 => 2.65,
          7 => 3.95,
          // The rest are added by us.
          8 => 4.75,
          9 => 6.0,
          10 => 7.5,
        ];
        
        // Check for '+' and '-' prefixes.
        if ($data[0] === '+' || $data[0] === '-') {
          $prefix = $data[0];
          $minmax = $unit_minmax['pt'];
          $value = floatval(substr($data, 1));
          $value = $prefix === '+' ? ($base_size + $value) : ($base_size - $value);
          $value = max($minmax[0], min($minmax[1], $value));
          $data = $value . 'pt';
          return;
        }
        
        // Check for 'px', 'pt', 'em', 'rem' suffixes.
        // Other CSS suffixes exist, but we don't support them.
        if (preg_match('/(px|pt|em|rem|%)$/', $data, $matches)) {
          $unit = $matches[0];
          $minmax = $unit_minmax[$unit];
          $value = substr($data, 0, -strlen($unit));
          $value = max($minmax[0], min($minmax[1], $value));
          $data = $value . $unit;
          return;
        }
        
        // Check for plain sizes, e.g. 10, 15. Up to 10, we have static sizes;
        // above that, we assume these are 'px' values.
        if (is_numeric($data)) {
          $minmax = $unit_minmax['px'];
          $value = floatval($data);
          // Return static sizes.
          if ($value > 0 && $value <= 10) {
            $data = $static_sizes[ceil($value)] . 'em';
            return;
          }
          $value = max($minmax[0], min($minmax[1], $value));
          $data = $value . 'px';
          return;
        }
        
        // If it's not one of these, just use regular font size.
        $data = '100%';
      },
    );

    // Inline version of the center tag.
    //
    // This is necessary to permit [center] to work in inline context.
    // [center]Centered text[/center]
    $bbc_center_inline = array(
      'tag' => 'center',
      'before' => '<center>',
      'after' => '</center>',
      'block_level' => false,
    );
  
    $codes[] = $bbc_youtube;
    $codes[] = $bbc_irc;
    $codes[] = $bbc_hide;
    $codes[] = $bbc_hide_pe;
    $codes[] = $bbc_spoiler;
    $codes[] = $bbc_dohtml;
    $codes[] = $bbc_quote_no_link;
    $codes[] = $bbc_size;
    $codes[] = $bbc_size_catchall;

    GWBBC::replaceCode('center', $bbc_center_inline, $codes);
    GWBBC::modifyCode('left', ['block_level' => false], $codes);
    GWBBC::modifyCode('right', ['block_level' => false], $codes);
  }

  public static function modifyCode($tag_name, $update, &$codes) {
    // Update all existing tags.
    foreach ($codes as $k => $v) {
      if ($v['tag'] !== $tag_name) {
        continue;
      }
      $codes[$k] = array_merge($v, $update);
    }
  }

  public static function replaceCode($tag_name, $new_tag, &$codes) {
    // Remove all existing tags by this tag name.
    foreach ($codes as $k => $v) {
      if ($v['tag'] === $tag_name) {
        unset($codes[$k]);
      }
    }

    // Insert the new tag.
    $codes[] = $new_tag;
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
