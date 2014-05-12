{* Smarty *}

{strip}
{foreach from=$titles key=i item=t}
{if $i > 0}, aka {/if}<strong>{if $user.userid == 1}<a href="{$smarty.server.PHP_SELF}?text={$t|escape:url}">{/if}{$t|escape:html}{if $user.userid == 1}</a>{/if}</strong>
{/foreach}
{/strip}