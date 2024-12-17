{extends file='customer/page.tpl'}

{block name='page_title'}
    {l s='Mis Puntos de Fidelidad' mod='mipuntos'}
{/block}

{block name='page_content'}
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 loyalty-points-card">
        <div class="card text-center shadow">
            <span class="icon-wrapper">
                <i class="material-icons loyalty-icon">&#xe8e8;</i>
            </span>
            <h4 class="card-title">{l s='Tus Puntos Acumulados' mod='mipuntos'}</h4>
            <p class="card-points">
                <strong>{$points}</strong> {l s='puntos de fidelidad' mod='mipuntos'}
            </p>
        </div>
    </div>
</div>
{/block}