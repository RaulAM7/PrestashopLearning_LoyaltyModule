{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='Mis Puntos de Fidelidad' mod='mipuntos'}
{/block}

{block name='page_content'}
    <div class="loyalty-points">
        <h3>{l s='Tus Puntos Acumulados' mod='mipuntos'}</h3>
        <p>
            {l s='Actualmente tienes' mod='mipuntos'} <strong>{$points}</strong> {l s='puntos de fidelidad.' mod='mipuntos'}
        </p>
    </div>
{/block}
