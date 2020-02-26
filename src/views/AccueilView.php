<?php

namespace mywishlist\views;

/**
 * Classe AccueilView, vue de la page d'accueil
 * @package mywishlist\views
 */
class AccueilView
{
    /**
     * Fonction utilisée pour faire le rendu
     */
    public function render()
    {
        $body = <<<BODY
<div id="content">
    <div id="content-inner">
    
         <h1>Page d'accueil</h1>
         <p>Bienvenue sur notre site ! Inscrivez-vous ou connectez-vous !</p>
        
        <div class="clr"></div>
    </div>
</div>
BODY;
        ViewRendering::render($body);
    }
}