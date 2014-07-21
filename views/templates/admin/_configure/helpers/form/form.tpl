{extends file="helpers/form/form.tpl"}

{block name="input"}

    {if $input.type == 'links'}

		{assign var=links value=$input.values}

		<table cellspacing="0" cellpadding="0" class="table" style="min-width:40em;">
			<tr>
				<!--<th><input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$input.name}', this.checked)" /></th>-->
				<th>{l s='Name' mod='blocketdtopmenu'}</th>
				<th>{l s='Status' mod='blocketdtopmenu'}</th>
				<th>{l s='Ordering' mod='blocketdtopmenu'}</th>
				<th>{l s='Access' mod='blocketdtopmenu'}</th>
				<th>{l s='Menu Item Type' mod='blocketdtopmenu'}</th>
				<!--<th>{l s='Home' mod='blocketdtopmenu'}</th>-->
				<th>{l s='ID' mod='blocketdtopmenu'}</th>
				<th>{l s='Actions' mod='blocketdtopmenu'}</th>
			</tr>

			{foreach $links as $key => $link}
				{$orderkey = array_search($link.id, $input.ordering[$link.parent_id])}

				<tr {if $link@iteration % 2}class="alt_row"{/if}>
					<!--<td>
						<input type="checkbox" class="cmsBox" name="{$input.name}" id="link_{$link.id}" value="{$link.id}">
					</td>-->
					<td>
						<strong>{str_repeat('|&mdash;', ($link.level - 1))}{if $link.level > 1}&nbsp;{/if}<a href="{$current}&token={$token}&editLink&id_link={(int)$link.id}" title="{l s='Edit' mod='blocketdtopmenu'}">{$link.title}</a></strong>
					</td>
					<td align="center">
						<img src="{$smarty.const._PS_ADMIN_IMG_}{if $link.published}enabled.gif{else}disabled.gif{/if}" alt="{if $link.published}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $link.published}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
					</td>
					<td>
						{if isset($input.ordering[$link.parent_id][$orderkey - 1])}
						<a href="{$current}&token={$token}&orderUp&id_link={(int)$link.id}">
							<img src="{$smarty.const._PS_ADMIN_IMG_}{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down'}" title="{l s='Down'}" />
						</a>
						{/if}
						{if isset($input.ordering[$link.parent_id][$orderkey + 1])}
							<a href="{$current}&token={$token}&orderDown&id_link={(int)$link.id}">
							<img src="{$smarty.const._PS_ADMIN_IMG_}{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up'}" title="{l s='Up'}" />
						</a>
						{/if}
					</td>
					<td>
						{if $link.access == 0}
							{l s='Public'}
						{elseif $link.access == 1}
							{l s='Guests'}
						{elseif $link.access == 2}
							{l s='Customers'}
						{/if}
					</td>
					<td>
						{if $link.type == 'page'}
							{l s='Page'}
						{elseif $link.type == 'pcategory'}
							{l s='Product Category'}
						{elseif $link.type == 'product'}
							{l s='Product'}
						{elseif $link.type == 'cms'}
							{l s='CMS Page'}
						{elseif $link.type == 'ccategory'}
							{l s='CMS Category'}
						{elseif $link.type == 'supplier'}
							{l s='Supplier'}
						{elseif $link.type == 'manufacturer'}
							{l s='Manufacturer'}
						{elseif $link.type == 'manufacturer'}
							{l s='Manufacturer'}
						{elseif $link.type == 'module'}
							{l s='Module'}
						{elseif $link.type == 'separator'}
							{l s='Separator'}
						{else}
							{l s='Unknown'}
						{/if}
					</td>
					<!--<td align="center">
						<img src="{$smarty.const._PS_ADMIN_IMG_}{if $link.home}enabled.gif{else}disabled.gif{/if}" alt="{if $link.home}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $link.home}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
					</td>-->
					<td>
						{$link.id}
					</td>
					<td>
						<a href="{$current}&token={$token}&editLink&id_link={(int)$link.id}" title="{l s='Edit' mod='blocketdtopmenu'}"><img src="{$smarty.const._PS_ADMIN_IMG_}edit.gif" alt="" /></a>
						<a href="{$current}&token={$token}&deleteLink&id_link={(int)$link.id}" title="{l s='Delete' mod='blocketdtopmenu'}"><img src="{$smarty.const._PS_ADMIN_IMG_}delete.gif" alt="" /></a>
					</td>
				</tr>

			{/foreach}

		</table>

	{elseif $input.type == 'select_parent'}

		<select name="{$input.name}" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"{if isset($input.size)} size="{$input.size}"{/if}{if isset($input.class)} class="{$input.class}"{/if}>
			<option value="1">{l s='Root Menu Link'}</option>
		{foreach $input.values as $link}
			{if $input.current != $link.id}
			<option value="{$link.id}"{if $fields_value[$input.name] == $link.id} selected{/if}>{str_repeat('-&nbsp;', ($link.level))}{$link.title}</option>
			{/if}
		{/foreach}
		</select>

	{else}
		{$smarty.block.parent}
    {/if}

{/block}
