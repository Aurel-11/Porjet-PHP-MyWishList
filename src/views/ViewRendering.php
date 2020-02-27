<?php


namespace mywishlist\views;

use mywishlist\utils\Authentication;
use Slim\Slim;

/**
 * Classe ViewRendering, classe qui ajoute le footer, header, css, etc au code html généré pas les vues
 * @package mywishlist\views
 */
class ViewRendering
{

    /**
     * Fonction permettant d'obtenir le code html des liens cliquable en fonction de l'utilisateur
     * @return string html généré
     */
    private static function getTopNav()
    {
        $app = Slim::getInstance();
        $lists = $app->urlFor('listPublic');
        $account = $app->urlFor('account');
        $accountLists = $app->urlFor('accountLists');
        $accountEdit = $app->urlFor('accountEdit');
        $accountLogout = $app->urlFor('accountLogout');
        if (Authentication::getUserLevel() == Authentication::ANONYMOUS)
        {
            return <<<NAV
<li><a href=$lists>Listes public</a></li>
<li><a href=$account>S'inscrire/Se Connecter</a></li>
NAV;
        } else {
            $username = Authentication::getUsername();
            return <<<NAV
<li><a href=$lists>Listes public</a></li>
<li><a href=$accountLists>Mes listes</a></li>
<li><a href=$accountEdit>Bonjour, $username</a></li>
<li><a href=$accountLogout>Se Deconnecter</a></li>
NAV;
        }
    }

    /**
     * Fonction appelée pour faire le rendu final
     * @param string $body html à mettre en forme
     * @param string $title titre de la page (optionnel)
     */
    public static function render(string $body, string $title = "")
    {
        $app = Slim::getInstance();
        $accueil = $app->urlFor('accueil');
        $top_nav = self::getTopNav();
        echo <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>MyWishList$title</title>
    <link href ="/Projet-PHP-MyWishList/css/style.css" rel="stylesheet">
</head>
<body>
    <header id="header">
        <div id="header-inner">	
            <div id="logo">
                <h1><a href=$accueil>Wish<span>List</span></a></h1>
            </div>
            <div id="top-nav">
                <ul>
                    $top_nav
                </ul>
            </div>
            <div class="clr"></div>
        </div>
    </header>
    
    $body
    
    <footer id="footer">
        <div id="footer-inner">
            <p>Copyright - MyWishList Project - Lucas BURTÉ, Pierre MARCOLET, Emilien VISENTINI, Aurélien RETHIERS</p>
            <p><a href=$accueil>Accueil</a> &#124; <a href="#">CGU</a> &#124; <a href="#">CGV</a></p>
            <div class="clr"></div>
        </div>
    </footer>
    
</body>
</html>
HTML;

    }
}
