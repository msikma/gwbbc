[![MIT license](https://img.shields.io/badge/license-MIT-brightgreen.svg)](https://opensource.org/licenses/MIT)

# GW BBC

A mod for [SMF 2.0.19](https://www.simplemachines.org/) (Simple Machine Forums) that adds several new BBC (Bulletin Board Code) tags.

It's made specifically for the [Gaming World Forums](https://github.com/msikma/gw2006).

This mod uses [SMF integration hooks](https://wiki.simplemachines.org/smf/integration_hooks) and doesn't require any modifications to the core files. It can be installed through the package manager in the admin panel.

## Tags

The following tags are added:

<table>
<tr>
<td>[youtube]Q3ok1bN0PAk[/youtube]</td>
<td>Displays a Youtube embed for the given video ID.</td>
</tr>
<tr>
<td>[hide]post&nbsp;content[/hide]<br />[hide="reason"]post&nbsp;content[/hide]</td>
<td>Hides content and requires the user to click to see it.</td>
</tr>
<tr>
<td>[irc="irc://example.com"]link&nbsp;name[/hide]</td>
<td>Links to an IRC server.</td>
</tr>
<tr>
<td>[spoiler]post&nbsp;content[/spoiler]</td>
<td>Marks text as a spoiler and requires a click to reveal it. Like [hide], but inline instead of block.</td>
</tr>
<tr>
<td>[dohtml]html&nbsp;content[/dohtml]</td>
<td>Sanitizes HTML and then prints it.</td>
</tr>
</table>

The HTML sanitizer uses [symfony/html-sanitizer](https://packagist.org/packages/symfony/html-sanitizer) to do the actual work, and does the work of removing dangerous tags as well as fixing invalid HTML.

## License

MIT license.
