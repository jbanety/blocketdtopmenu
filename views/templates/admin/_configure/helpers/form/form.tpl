{**
 * @package     blocketdtopmenu
 *
 * @version     2.1
 * @copyright   Copyright (C) 2017 ETD Solutions. Tous droits réservés.
 * @license     https://raw.githubusercontent.com/jbanety/blocketdcustom/master/LICENSE
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
        {if isset($field.count)}<span class="badge">{$field.count}</span>{/if}
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

	{elseif $input.type == 'columns_widths'}
		{$values = $fields_value[$input.name]}
		<div class="columns_widths">
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][1]" placeholder="Col 1" value="{if !empty($values)}{$values->{'1'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][2]" placeholder="Col 2" value="{if !empty($values)}{$values->{'2'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][3]" placeholder="Col 3" value="{if !empty($values)}{$values->{'3'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][4]" placeholder="Col 4" value="{if !empty($values)}{$values->{'4'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][5]" placeholder="Col 5" value="{if !empty($values)}{$values->{'5'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][6]" placeholder="Col 6" value="{if !empty($values)}{$values->{'6'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][7]" placeholder="Col 7" value="{if !empty($values)}{$values->{'7'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][8]" placeholder="Col 8" value="{if !empty($values)}{$values->{'8'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][9]" placeholder="Col 9" value="{if !empty($values)}{$values->{'9'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][10]" placeholder="Col 10" value="{if !empty($values)}{$values->{'10'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][11]" placeholder="Col 11" value="{if !empty($values)}{$values->{'11'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<div style="float:left">
				<input type="text" class="form-control" name="params[columns_widths][12]" placeholder="Col 12" value="{if !empty($values)}{$values->{'12'}|escape:'html':'UTF-8'}{/if}">
			</div>
			<span class="clearfix"></span>
		</div>
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
				<table class="table">
					<thead>
					<tr>
						<th>{l s='Name' mod='blocketdtopmenu'}</th>
						<th class="text-center">{l s='Status' mod='blocketdtopmenu'}</th>
						<th class="text-center">{l s='Ordering' mod='blocketdtopmenu'}</th>
						<th>{l s='Access' mod='blocketdtopmenu'}</th>
						<th>{l s='Menu Item Type' mod='blocketdtopmenu'}</th>
						<th>{l s='ID' mod='blocketdtopmenu'}</th>
						<th class="title_box text-right">{l s='Actions' mod='blocketdtopmenu'}</th>
					</tr>
					</thead>
					<tbody>
					{foreach $links as $key => $link}
						{$orderkey = array_search($link.id, $input.ordering[$link.parent_id])}
						<tr {if $key%2}class="alt_row"{/if}>
							<td>
								<strong>{str_repeat('|&mdash;', ($link.level - 1))}{if $link.level > 1}&nbsp;{/if}<a href="{$current}&token={$token}&editLink&id_link={(int)$link.id}" title="{l s='Edit' mod='blocketdtopmenu'}">{$link.title}</a></strong>
							</td>
							<td class="text-center">
								<i class="icon-{if $link.published}check{else}times{/if}"></i>
							</td>
							<td class="text-center">
								{if isset($input.ordering[$link.parent_id][$orderkey - 1])}
									<a href="{$current}&token={$token}&orderUp&id_link={(int)$link.id}" class="btn btn-link">
										<i class="icon-caret-square-o-up"></i>
									</a>
								{/if}
								{if isset($input.ordering[$link.parent_id][$orderkey + 1])}
									<a href="{$current}&token={$token}&orderDown&id_link={(int)$link.id}" class="btn btn-link">
										<i class="icon-caret-square-o-down"></i>
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
												<a href="{$current}&token={$token}&deleteLink&id_link={(int)$link.id}" title="{l s='Delete' mod='blocketdtopmenu'}" onclick="return confirm('{l s='Do you really want to delete this link?' mod='blocketdtopmenu'}');">
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
		{else}
			<p>{l s='No links have been created.' mod='blocketdtopmenu'}</p>
		{/if}

	{else}
		{$smarty.block.parent}
	{/if}

{/block}
