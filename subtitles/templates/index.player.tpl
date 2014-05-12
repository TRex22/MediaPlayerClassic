{if !empty($movies)}
ticket={$ticket}
{foreach from=$movies item=m}
movie={foreach from=$m.titles item=t}{$t}|{/foreach}

{foreach from=$m.subs item=s}
{if !empty($s.files)}
subtitle={$s.ms_id}
name={$s.name}
discs={$s.discs}
disc_no={$s.disc_no}
format={$s.format}
iso639_2={$s.iso639_2}
language={$s.language}
nick={$s.nick}
email={$s.email}
endsubtitle
{/if}
{/foreach}
endmovie
{/foreach}
end
{/if}

{* 5th line is empty because smarty gets confused if not *}