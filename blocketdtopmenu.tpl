{**
 * @package     blocketdtopmenu
 *
 * @version     2.2.0
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 *}
{strip}
{addScript src=$modules_dir|cat:"blocketdtopmenu/js/menu.js"}
{$breakCount = []}
{$count = []}
{$currentCol = []}
{$colWidths = []}
{$groupChildren = []}
{$id = ''}
{if $tagId|strlen > 0}
    {$id = ' id="'|cat:$tagId|cat:'"'}
{addScriptDeclaration content="jQuery(document).ready(function() {ldelim}
    \$('#$tagId').etdtopmenu();
{rdelim});"}
{/if}
{/strip}
<!--[ETDHOOK:NAVIGATION]-->
<nav class="navbar{if $tagClass|strlen > 0} {$tagClass}{/if}"{$id}>
    <div class="container">
        <div class="row">
            <ul class="nav navbar-nav">
                <li id="navbar-close" class="visible-xs visible-sm"><a href="#">{l s='Close'} <span class="fa fa-times-circle"></span></a></li>
{foreach $list as $i => $item}
{strip}

    {$class = 'item-'|cat:$item.id}
    {$ulClass = ''}

    {if $item.css|strlen > 0}
        {$class = $class|cat: ' '|cat:$item.css}
    {/if}

    {if $item.children > 0 && $item.params->columns > 1}
        {$breakCount[$item.level+1] = floor($item.children / $item.params->columns)}
        {if $breakCount[$item.level+1] == 0 && $item.children / $item.params->columns > 0}
            {$breakCount[$item.level+1] = 1}
        {/if}
        {$groupChildren[$item.level+1] = $item.params->group_child_items}
        {$count[$item.level+1] = 0}
        {$currentCol[$item.level+1] = 0}
        {$colWidths[$item.level+1] = $item.params->columns_widths}
        {$ulClass = ' class="row"'}
    {/if}

    {if $item.deeper}
        {if $item.level == 1}
            {$class = $class|cat:' dropdown'}
        {/if}
    {/if}

    {if isset($breakCount[$item.level]) && $breakCount[$item.level] > 0}
        {if $count[$item.level] % $breakCount[$item.level] == 0}
            {$currentCol[$item.level] = $currentCol[$item.level] + 1}
            {if isset($groupChildren[$item.level]) && $groupChildren[$item.level] }
                {if $item.deeper}
                    {$class=$class|cat:" ":$colWidths[$item.level]->{$currentCol[$item.level]}}
                {else}
                    {if $count[$item.level] > 0}
                        </ul></li>
                    {/if}
                    <li class="{$colWidths[$item.level]->{$currentCol[$item.level]}}"><ul>
                {/if}
            {/if}
        {/if}
        {$count[$item.level] = $count[$item.level] + 1}
    {/if}

{/strip}
                <li class="{$class}">
                    {if $item.params->group_child_items == 0 || $item.params->group_child_items && $item.params->show_title}{include $item_tpl item=$item}{/if}
                {if $item.deeper}
                    {if $item.level == 1}
                    <div class="subnavbar">
                        <div class="container">
                    {/if}
                        <ul{$ulClass}>
                            <li class="subnavbar-close visible-xs visible-sm"><a href="#">{l s='Close'} <span class="fa fa-chevron-circle-left"></span></a></li>
                {elseif $item.shallower}
                    </li>
                    {strip}
                    {for $j = 0; $j < $item.level_diff; $j++ }
                        {if isset($breakCount[$item.level - $j]) && $breakCount[$item.level - $j] > 0}
                            {if isset($groupChildren[$item.level - $j]) && $groupChildren[$item.level - $j] }
                                </ul></li>
                                </ul>
                                {if $item.level - $j == 2}
                </div></div>
                                 {/if}
                            {/if}
                            {$breakCount[$item.level - $j] = 0}
                            {$groupChildren[$item.level - $j] = 0}
                            {$count[$item.level - $j] = 0}
                            {$currentCol[$item.level - $j] = 0}
                            {$colWidths[$item.level - $j] = null}
                        {/if}
                    {/for}
                    {/strip}
                    </li>
                {else}
                    </li>
                {/if}
            {/foreach}
            </ul>
        </div>
    </div>
</nav>
<!--[/ETDHOOK:NAVIGATION]-->