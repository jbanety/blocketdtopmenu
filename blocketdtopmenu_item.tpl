{**
 * @package     blocketdtopmenu
 *
 * @version     2.2.0
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 *}
{strip}
{$target = ''}
{$onclick = ''}
{$toggle = ''}
{$href = ''}
{$tag = 'a'}
{$anchor_class = ''}

{if $item.type == 'heading'}
    {$tag = 'span'}
    {$anchor_class = 'menu-heading'}
{elseif $item.type == 'separator'}
    {$href = '#'}
{/if}

{if $item.browserNav == 1}
    {$target = ' target="_blank"'}
{elseif $item.browserNav == 2}
    {$onclick = ' onclick="window.open(this.href, \'targetWindow\', \'"toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\'); return false;"'}
{/if}

{if $item.deeper && $item.level == 1 }
    {$toggle = ' data-toggle="subnavbar"'}
{/if}

{if isset($item.link)}
    {$href = $item.link}
{/if}
{if $href|strlen > 0 || $toggle|strlen > 0 || $item.type == 'heading'}<{$tag}{if $href|strlen > 0} href="{$href}"{$target}{$onclick}{/if}{if !empty($anchor_class)} class="{$anchor_class}"{/if}{$toggle}>{/if}
    {if $item.image|strlen > 0}
        <img class="menu-image" src="{$item.image}" alt="{htmlspecialchars($item.title, $smarty.const.ENT_QUOTES, 'UTF-8')}">
    {/if}
    <span class="menu-text">{htmlspecialchars($item.title, $smarty.const.ENT_QUOTES, 'UTF-8')}</span>
    {if $item.deeper && $item.level == 1 }<span class="fa fa-chevron-circle-right visible-sm visible-xs"></span>{/if}
{if $href|strlen > 0 || $toggle|strlen > 0 || $item.type == 'heading'}</{$tag}>{/if}
{/strip}