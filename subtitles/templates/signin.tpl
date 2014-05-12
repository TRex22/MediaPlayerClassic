{* Smarty *}

<div class="signin" align="center">

	{if $user.userid > 0}
	
	<p align="center">You are now signed in!</p>
	
	{else}

	<form action="{$smarty.server.PHP_SELF}" method="POST">
	
	<h1>Sign in</h1>
	
	<table>
	<tr class="required{if isset($err.nick)} invalid{/if}">
		<th>Username</th>
		<td><input class="text blueborder" type="text" name="nick" value="{$nick|escape:"quote"}" /></td>
	</tr>
	<tr class="required{if isset($err.password)} invalid{/if}">
		<th>Password</th>
		<td><input class="text blueborder" type="password" name="password" /></td>
	</tr>
	<tr class="optional{if isset($err.email)} invalid{/if}">
		<th>Email</th>
		<td><input class="text blueborder" type="text" name="email" value="{$email|escape:"quote"}" /></td>
	</tr>
	</table>
	
	<div style="padding-top: 10px;" align="center">
		<input type="submit" name="signin" value="Sign in" />
		<input type="submit" name="register" value="Register" />
		<br>
		<label><input type="checkbox" name="rememberme" {if $rememberme}checked{/if}/>Remember me</label>
	</div>

	</form>

	{/if}
	
</div>