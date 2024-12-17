{**
 * Plantilla: loyalty_offer.tpl
 * Descripción: Vista para la oferta de puntos de fidelidad.
 *}

<div class="container loyalty-offer-page">
    <h1>{$loyalty_points_offer.title}</h1>
    <p>{$loyalty_points_offer.description}</p>
    <ul>
        {foreach from=$loyalty_points_offer.benefits item=benefit}
            <li>{$benefit}</li>
        {/foreach}
    </ul>
    <div class="cta">
        <a href="{$urls.pages.identity}" class="btn btn-primary">{$loyalty_points_offer.cta}</a>
    </div>
</div>

<style>
.loyalty-offer-page {
    text-align: center;
    padding: 2rem;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin: 2rem auto;
    max-width: 800px;
    font-family: 'Nunito Sans', sans-serif;
}

.loyalty-offer-page h1 {
    color: #2cd8d5;
    margin-bottom: 1rem;
}

.loyalty-offer-page ul {
    text-align: left;
    margin: 1rem auto;
    display: inline-block;
    list-style: none;
}

.loyalty-offer-page li {
    margin-bottom: 0.5rem;
}

.cta .btn {
    background-color: #ff3860;
    color: #fff;
    font-weight: bold;
    padding: 0.75rem 1.5rem;
    text-transform: uppercase;
    transition: background-color 0.3s;
}

.cta .btn:hover {
    background-color: #2cd8d5;
    text-decoration: none;
}
</style>
