{**
 * @package     blocketdtopmenu
 *
 * @version     2.1
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 *}
{strip}
{$target = ''}
{$onclick = ''}
{$toggle = ''}
{$href = ''}

{if $item.browserNav == 1}
    {$target = ' target="_blank"'}
{elseif $item.browserNav == 2}
    {$onclick = ' onclick="window.open(this.href, \'targetWindow\', \'"toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\'); return false;"'}
{/if}

{if $item.deeper && $item.level == 1 }
    {$toggle = ' data-toggle="subnavbar"'}
{/if}

{if $item.type == "separator"}
    {$href = '#'}
{elseif $item.type == "pcategory"}

{/if}
<a href="{$href}"{$target}{$onclick}{$toggle}>{htmlspecialchars($item.title, $smarty.const.ENT_QUOTES, 'UTF-8')}{if $item.deeper && $item.level == 1 }<span class="fa fa-chevron-circle-right visible-sm visible-xs"></span>{/if}</a>
{/strip}