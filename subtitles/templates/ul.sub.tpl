{* Smarty *}

	<table>
	
	<tr class="required{if isset($err.isolang_sel[$n])} invalid{/if}">
		<th>Language</th>
		{strip}
		<td>
		<select name="isolang_sel[{$n}]">
		<option value=""{if $isolang_sel[$n] == ""} selected="selected"{/if}>Please select a language...</option>
		{foreach from=$isolang key=code item=name}
		<option value="{$code}"{if $code == $isolang_sel[$n]} selected="selected"{/if}>{$name|truncate:40:"...":true}</option>
		{/foreach}
		</select>
		</td>
		{/strip}
	</tr>

	<tr class="required{if isset($err.format_sel[$n])} invalid{/if}">
		<th>File format</th>
		{strip}
		<td>
		<select name="format_sel[{$n}]">
		<option value=""{if $format_sel[$n] == ""} selected="selected"{/if}>Please select a file format...</option>
		{foreach from=$format item=code}
		<option value="{$code}"{if $code == $format_sel[$n]} selected="selected"{/if}>{$code|truncate:40:"...":true|escape:"html"}</option>
		{/foreach}
		</select>
		</td>
		{/strip}
	</tr>

	<tr class="required{if isset($err.disc_no[$n])} invalid{/if}">
		<th>Disc number</th>
		<td>
			<input class="number" type="text" name="disc_no[{$n}]" value="{$disc_no[$n]|escape:"quotes"}" size="1" />
			out of 
			<input class="number" type="text" name="discs[{$n}]" value="{$discs[$n]|escape:"quotes"}" size="1" />
		</td>
	</tr>

	{if !empty($file)}
	<tr class="required{if isset($err.file_sel[$n])} invalid{/if}">
		<th>Video</th>
		{strip}
		<td>
		<select name="file_sel[{$n}]">
		<option value=""{if $file_sel[$n] == 0} selected="selected"{/if}>Please select a file...</option>
		{foreach from=$file key=i item=f}
		<option value="{$i}"{if $i == $file_sel[$n]} selected="selected"{/if}>{$i}. {$f.name|truncate_mid:30|escape:"html"} ({$f.intsize/1024|string_format:"%d"} KB)</option>
		{/foreach}
		</select>
		</td>
		{/strip}
	</tr>
	{/if}

	<tr class="optional{if isset($err.notes[$n])} invalid{/if}">
		<th>Notes</th>
		<td><input class="text" type="text" name="notes[{$n}]" value="{$notes[$n]|escape:"quotes"}" /></td>
	</tr>

	<tr class="required{if isset($err.sub[$n])} invalid{/if}">
		<th>File to upload</th>
		<td>
			<input type="hidden" name="MAX_FILE_SIZE" value="900000" />
			<input type="file" name="sub[{$n}]" />
		</td>
	</tr>
	
	</table>
