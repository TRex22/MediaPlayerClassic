{* Smarty *}

<div class="search" align="center">

	<form action="{$smarty.server.PHP_SELF}" method="GET" accept-charset="utf-8">
	
	<h1>Search</h1>
	
	<table>
	<tr>
		<th>Title</th>
		<td onmouseover="return overlib('Wildcards are allowed (*, ?)', WRAP);" onmouseout="return nd();">
			<input class="text blueborder" type="text" name="text" value="{$text|escape:"quote"}" />
		</td>
	</tr>
	<tr>
		<th>Disc(s)</th>
		<td><input class="blueborder" type="text" name="discs" {if !empty($discs)}value="{$discs|escape:"quote"}"{/if} size="1" /></td>
	</tr>
	<tr>
		<th>Language</th>
		<td>
		<select class="blueborder" name="isolang_sel">
		<option value=""{if $isolang_sel == ""} selected="selected"{/if}>Any language</option>
		{foreach from=$isolang key=code item=name}
		<option value="{$code}"{if $code == $isolang_sel} selected="selected"{/if}>{$name|truncate:40:"...":true}</option>
		{/foreach}
		</select>
		</td>
	</tr>
	<tr>
		<th>File format</th>
		<td>
		<select name="format_sel">
		<option value=""{if $format_sel == ""} selected="selected"{/if}>Any file format</option>
		{foreach from=$format item=code}
		<option value="{$code}"{if $code == $format_sel} selected="selected"{/if}>{$code|truncate:40:"...":true|escape:"html"}</option>
		{/foreach}
		</select>
		</td>
	</tr>
	</table>
	
	{*<div align="center">* minimum 3 characters, wildcards (*,?) allowed</div>*}
	
	<div style="padding-top: 10px;" align="center">
		<input type="submit" value="Search" />
	</div>

	</form>

</div>

<div class="index">
{foreach from=$index item=i}
	{if $text != $i.mask}<a href="{$smarty.server.PHP_SELF}?text={$i.mask}&bw=1">{/if}{$i.label}{if $text != $i.mask}</a>{/if}
{/foreach}
</div>

<div class="results">

{if !empty($movies)}

	{if $page.total > $page.count}
	<p align="center">
	{if isset($page.prev)}<a href="{$smarty.server.PHP_SELF}?start={$page.prev}&{$search.q}">&laquo;</a>{/if}
	{if $page.has_less}..{/if}
	{foreach from=$page.index item=i}
		{strip}
		{if $i != $page.start}<a href="{$smarty.server.PHP_SELF}?start={$i}&{$search.q}">{/if}
		{$i/$page.limit+1}
		{if $i != $page.start}</a>{/if}
		{/strip}
	{/foreach}
	{if $page.has_more}..{/if}
	{if isset($page.next)}<a href="{$smarty.server.PHP_SELF}?start={$page.next}&{$search.q}">&raquo;</a>{/if}
	</p>
	{/if}
	
	<form action="{$smarty.server.PHP_SELF}" method="POST" accept-charset="utf-8">
	
	{if !empty($text)}<input type="hidden" name="text" value="{$text|escape:"quote"}" />{/if}
	{if !empty($discs)}<input type="hidden" name="discs" value="{$discs|escape:"quote"}" />{/if}
	{if !empty($isolang_sel)}<input type="hidden" name="isolang_sel" value="{$isolang_sel|escape:"quote"}" />{/if}
	{if !empty($format_sel)}<input type="hidden" name="format_sel" value="{$format_sel|escape:"quote"}" />{/if}

	<ol start="{$page.start+1}">
	{foreach from=$movies item=m}
		{if $browser != 'Opera'}<li>{/if} {* grrrr *}
		{if !empty($m.imdb)}<a href="http://www.imdb.com/title/tt{$m.imdb|string_format:"%07d"}/" class="imdb" target="_blank">[IMDb]</a>{/if}
		{if $browser == 'Opera'}<li>{/if}
		
		{if $user.userid == 1}
		<input type="checkbox" name="movie_id[{$m.id}]" />
		{/if}
	
		{include file="title.tpl" titles=$m.titles}<br>
		{*Last updated: <strong>{$m.updated|date_format:"%Y %b %e"}</strong><br>*}
		
		<br>
	
		<table cellpadding="0" cellspacing="0">
		<tr>
			<th>&nbsp</th>
			<th width="40%">File</th>
			<th>Disc</th>
			<th>Date</th>
			<th>Format</th>
			<th>Language</th>
			<th>Uploader</th>
		</tr>
		{foreach from=$m.subs item=s}
		<tr>
			<td class="icons">
			<nobr>
				{if !empty($s.has_file)}<img src="img/video.gif" />{/if}
				{if !empty($s.notes)}<img src="img/note.png" onmouseover="return overlib('{$s.notes|escape:"quote"}', WRAP);" onmouseout="return nd();" />{/if}
				{if $user.userid == 1 || $s.userid > 0 && $s.userid == $user.userid}<a href="{$smarty.server.PHP_SELF}?del={$s.ms_id}" onclick="return confirm('Are you sure you want to delete this?')"><img src="img/del.gif" /></a>{/if}
				{*$user.userid == 1 || *}
			</nobr>
			</td>
			<td class="name">
			{strip}
				<nobr>
				{if !empty($s.files)}<span class="dlme">&rarr;&nbsp;{/if}
				<a href="dl.php?id={$s.ms_id}&ticket={$ticket}">{$s.name|truncate_mid:50|escape:"html"}</a>
				{if !empty($s.files)}&nbsp;&larr;</span>{/if}
				</nobr>
			{/strip}
			</td>
			<td>{$s.disc_no}/{$s.discs}</td>
			<td><nobr>{$s.date|date_format:"%Y %b %e"}</nobr></td>
			<td>{$s.format|escape:"html"}</td>
			<td>{$s.language|escape:"html"}</td>
			<td>
				{if empty($s.email)}
				{$s.nick|escape:"html"}
				{else}
				{mailto address=$s.email text=$s.nick encode="hex"}
				{/if}
			</td>
		</tr>
		{/foreach}
		</table>
		<br>

		</li>
	{/foreach}
	</ol>

	{if $user.userid == 1}
	<div align="center">
		Change IMDb link of the selected titles to:<br>
		<input type="text" name="imdb_url" value="" />
		<input type="submit" name="submit" value="OK" />
	</div>
	{/if}
	
	</form>

{elseif !empty($files)}

<p align="center">
	Your search did not match any subtitles, would you like to 
	{strip}<a href="ul.php?
	{foreach from=$files key=i item=file}
		{foreach from=$file key=param item=value}
			{$param}[{$i}]={$value|escape:"url"}&
		{/foreach}
	{/foreach}{/strip}">upload</a> instead?
</p>

{elseif !empty($message)}

<p align="center">{$message|escape:"html"}</p>

{/if}

</div>