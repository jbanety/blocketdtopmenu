<ul class="nav navbar-nav"{if array_key_exists('tag_id',$layout->getArgs())} id="{$layout->getArgs('tag_id')}"{/if}>
    {foreach $menu->getChildren() as $item}
        {$layout->renderItem($item, $menu)}
    {/foreach}
</ul>