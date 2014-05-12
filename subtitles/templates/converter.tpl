{* Smarty *}

<form action="{$smarty.server.PHP_SELF}" method="POST">

<table border="0" style="margin-left: auto; margin-right: auto">
<tr>
	<td>
		Convert from
	</td>
	<td>
		<select name="intype">
		{foreach from=$intypes key=n item=label}
		<option value="{$n}" {if $n == $intype}selected{/if}>{$label|escape:"html"}</option>
		{/foreach}
		</select>
	</td>
	<td>
		to
	</td>
	<td>
		<select name="outtype">
		{foreach from=$outtypes key=n item=label}
		<option value="{$n}" {if $n == $outtype}selected{/if}>{$label|escape:"html"}</option>
		{/foreach}
		</select>
	</td>
	<td>
		@
	</td>
	<td>
		<input type="text" name="fps" value="{$fps|escape:"html"}" style="width: 3em;" />
	</td>
	<td>
		fps
	</td>
	<td>
		<input type="submit" value="Go!" />
	</td>
</tr>
</table>

<div align="center">
	<textarea name="text" wrap="soft" style="width: 90%; height: 50em; {if $conversion_error}border: 2px solid red{/if}">{$text|escape:"html"}</textarea>
</div>

</form>