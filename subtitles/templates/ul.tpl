{* Smarty *}

<div class="subul">

{if empty($file)}
<div class="warning">
	Warning:<br>You are trying to upload subtitles without binding them to their appropriate movie files,
	please use <a href="http://sf.net/projects/guliverkli/" target="_blank">Media Player Classic</a> (v6483+)
	to launch this page!
</div>
{/if}

{if $user.userid <= 0}
<div class="warning">
	Warning:<br>You are not signed in, anonymous uploads can't be managed later!
</div>
{/if}

<form action="{$smarty.server.PHP_SELF}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">

	<h1>Movie information</h1>
	
	<table>
	
	<tr class="optional{if isset($err.imdb_url)} invalid{/if}">
		<th><a href="http://imdb.com/{if !empty($guessedtitle)}find?tt=on;nm=on;mx=20;q={$guessedtitle|escape:"url"}{/if}" target="_blank">IMDb</a> (given this url, Title #1 can be left blank)</th>
		<td>
		{if !empty($imdb_titles)}
			<input type="hidden" name="imdb_url" value="{$imdb_url|escape:"quotes"}" />
			<a href="{$imdb_url|escape:"quotes"}" target="_blank">{$imdb_url|escape:"html"}</a>
			[<a href="{$smarty.server.PHP_SELF}?clearimdb=">edit</a>]<br>
			{include file="title.tpl" titles=$imdb_titles}<br>
		{else}
			<input class="text" type="text" name="imdb_url" value="{$imdb_url|escape:"quotes"}" />
		{/if}
		</td>
	</tr>
	
	{foreach from=$title key=i item=t}
	<tr class="{if $i == 0 && empty($imdb_titles)}required{else}optional{/if}{if isset($err.title[$i])} invalid{/if}">
		<th>Title #{$i+1}</th>
		<td><input class="text" type="text" name="title[{$i}]" value="{$t|escape:"quotes"}" /></td>
	</tr>
	{/foreach}
	
	</table>

	{if empty($imdb_titles)}
	<div style="padding-top: 10px;" align="center">
		<input type="submit" name="update" value="Update" />
	</div>
	{/if}
	
	{foreach from=$subs item=n}
	<h1><a href="javascript:void(0)" onclick="flip('sub{$n}')">Subtitle #{$n+1}</a></h1>
	<div class="{if $n == 0 || $isolang_sel[$n] != "" || $format_sel[$n] != "" || !empty($discs[$n]) || !empty($disc_no[$n]) || !empty($file_sel[$n]) || $notes[$n] != ""}shown{else}hidden{/if}" id="sub{$n}">
	{include file="ul.sub.tpl"}
	</div>
	{/foreach}

	<div style="padding-top: 10px;" align="center">
		<input type="submit" name="submit" value="Submit" />
	</div>
</form>

</div>