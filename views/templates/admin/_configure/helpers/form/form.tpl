{**
 * @package     blocketdtopmenu
 *
 * @version     1.6
 * @copyright   Copyright (C) 2015 Jean-Baptiste Alleaume. Tous droits réservés.
 * @license     http://alleau.me/LICENSE
 * @author      Jean-Baptiste Alleaume http://alleau.me
 *}
{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.type == 'links'}

	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="legend"}
	<h3>
		{if isset($field.image)}<img src="{$field.image}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
		{if isset($field.icon)}<i class="{$field.icon}"></i>{/if}
		{$field.title}
		<span class="panel-heading-action">
			{foreach from=$toolbar_btn item=btn key=k}
				{if $k != 'modules-list' && $k != 'back'}
					<a id="desc-{$table}-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if}" class="list-toolbar-btn" {if isset($btn.href)}href="{$btn.href}"{/if} {if isset($btn.target) && $btn.target}target="_blank"{/if}{if isset($btn.js) && $btn.js}onclick="{$btn.js}"{/if}>
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s=$btn.desc}" data-html="true">
							<i class="process-icon-{if isset($btn.imgclass)}{$btn.imgclass}{else}{$k}{/if} {if isset($btn.class)}{$btn.class}{/if}" ></i>
						</span>
					</a>
				{/if}
			{/foreach}
			</span>
	</h3>
{/block}

{block name="input"}

    {if $input.type == 'select_parent'}

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

{block name="input_row"}

	{if $input.type == 'hidden'}
		<input type="hidden" name="{$input.name}" id="{$input.name}" value="{$fields_value[$input.name]|escape:'html':'UTF-8'}" />
	{elseif $input.type == 'links'}

		{assign var=links value=$input.values}
		{if isset($links) && count($links) > 0}
			<div class="row">
				<div class="col-lg-12">
					<div class="panel">
						<div class="panel-heading">
							{$input.label}
						</div>
						<table class="table">
							<thead>
							<tr>
								<th>{l s='Name' mod='blocketdtopmenu'}</th>
								<th>{l s='Status' mod='blocketdtopmenu'}</th>
								<th>{l s='Ordering' mod='blocketdtopmenu'}</th>
								<th>{l s='Access' mod='blocketdtopmenu'}</th>
								<th>{l s='Menu Item Type' mod='blocketdtopmenu'}</th>
								<th>{l s='ID' mod='blocketdtopmenu'}</th>
								<th>{l s='Actions' mod='blocketdtopmenu'}</th>
							</tr>
							</thead>
							<tbody>
							{foreach $links as $key => $link}
								{$orderkey = array_search($link.id, $input.ordering[$link.parent_id])}
								<tr {if $key%2}class="alt_row"{/if}>
									<td>
										<strong>{str_repeat('|&mdash;', ($link.level - 1))}{if $link.level > 1}&nbsp;{/if}<a href="{$current}&token={$token}&editLink&id_link={(int)$link.id}" title="{l s='Edit' mod='blocketdtopmenu'}">{$link.title}</a></strong>
									</td>
									<td align="center">
										<img src="{$smarty.const._PS_ADMIN_IMG_}{if $link.published}enabled.gif{else}disabled.gif{/if}" alt="{if $link.published}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $link.published}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
									</td>
									<td>
										{if isset($input.ordering[$link.parent_id][$orderkey - 1])}
											<a href="{$current}&token={$token}&orderUp&id_link={(int)$link.id}">
												<img src="{$smarty.const._PS_ADMIN_IMG_}up.gif" alt="{l s='Up'}" title="{l s='Up'}" />
											</a>
										{/if}
										{if isset($input.ordering[$link.parent_id][$orderkey + 1])}
											<a href="{$current}&token={$token}&orderDown&id_link={(int)$link.id}">
												<img src="{$smarty.const._PS_ADMIN_IMG_}down.gif" alt="{l s='Down'}" title="{l s='Down'}" />
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
									<td>
										{$link.id}
									</td>
									<td>
										<div class="btn-group-action">
											<div class="btn-group pull-right">
												<a class="btn btn-default" href="{$current}&token={$token}&editLink&id_link={(int)$link.id}" title="{l s='Edit' mod='blocketdtopmenu'}">
													<i class="icon-edit"></i> {l s='Edit' mod='blocketdtopmenu'}
												</a>
												<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<i class="icon-caret-down"></i>&nbsp;
												</button>
												<ul class="dropdown-menu">
													<li>
														<a href="{$current}&token={$token}&deleteLink&id_link={(int)$link.id}" title="{l s='Delete' mod='blocketdtopmenu'}">
															<i class="icon-trash"></i> {l s='Delete' mod='blocketdtopmenu'}
														</a>
													</li>
												</ul>
											</div>
										</div>
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		{else}
			<p>{l s='No links have been created.' mod='blocketdtopmenu'}</p>
		{/if}

	{else}
		{$smarty.block.parent}
	{/if}

{/block}
