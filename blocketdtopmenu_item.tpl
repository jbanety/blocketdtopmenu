<li{if $item->hasListItemClasses()} class="{$item->getListItemClasses()}"{/if}{if ($item->hasCssId() && $layout->activeid)} id="{$item->getCssId()}"{/if}>
    <a{if ($item->hasLinkClasses())} class="{$item->getLinkClasses()}"{/if}{if ($item->hasLink())} href="{$item->getLink()}"{/if}{if ($item->hasTarget())} target="{$item->getTarget()}"{/if}{if ($item->hasAttribute('onclick'))} onclick="{$item->getAttribute('onclick')}"{/if}{if ($item->hasLinkAttribs())} {$item->getLinkAttribs()}{/if}>
        {$item->getTitle()} {if ($item->hasChildren())}<span class="fa fa-caret-down"></span>{/if}
    </a>
    {if ($item->hasChildren())}
        <ul class="dropdown-menu" role="menu"{$wrapper_css}>
            {foreach $item->getChildren() as $child}
                {$layout->renderItem($child, $menu)}
            {/foreach}
        </ul>
    {/if}
</li>