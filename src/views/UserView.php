<?php


namespace mywishlist\views;

use mywishlist\utils\Selection;
use Slim\Slim;

/**
 * Classe UserView, elle a pour but de gérer l'affichage en rapport à l'utilisateur et les actions liées à son compte.
 * @package mywishlist\views
 */
class UserView
{

    /**
     * @var $list array Elle peut être nulle ou pas, elle sert juste à passer des paramètres à sortir au rendu.
     * @var $content string Il s'agit de tout le code html qui contient le contenu.
     * @var $selecteur string Il s'agit de la variable contenant le type html à génèrer.
     * @var $app Slim Instance de Slim.
     */
    private $list, $selecteur, $content, $app;

    /**
     * UserView constructor.
     * @param $l array Elle peut être nulle ou pas, elle sert juste à passer des paramètres à sortir au rendu.
     * @param $s string Il s'agit de la variable contenant le type html à génèrer.
     */
    public function __construct($l, $s)
    {
        $this->list = $l;
        $this->selecteur = $s;
        $this->app = Slim::getInstance();
    }

    /**
     * Fonction qui a pour but de creér le formulaire de connexion et d'incription.
     * @return string Html génèré.
     */
    private function accountRegisterAndLogin()
    {
        $register = $this->app->urlFor('accountRegisterP');
        $login = $this->app->urlFor('accountLoginP');
        return <<<BODY
<div id="user-form">
    <div id="register-div" class="user-form">
        <h2>Creation d'un compte</h2>
        <form method="post" action=$register>
            <label>Nom d'utilisateur :</label><br>
            <input type="text" name="username" required><br>
            <label>Email :</label><br>
            <input type="email" name="email" required><br>
            <label>Confirmer l'email :</label><br>
            <input type="email" name="email-confirm" required><br>
            <label>Mot de passe :</label><br>
            <input type="password" name="password" required><br>
            <label>Confirmer le mot de passe :</label><br>
            <input type="password" name="password-confirm" required><br>
            
            
            <input type="checkbox" name="terms-of-use" value="iAgree" id="checkbox" class="css-checkbox" required/>
			<label for="checkbox" class="css-label">J'ai lu et j'accepte la <a href="/cgu">Clauses D'utilisation</a></label><br>
			
            
            
            <button type="submit" name="submit" value="doRegister">Créer mon compte</button>
        </form>
    </div>
    <div id="login-div" class="user-form">
        <h2>Connection a un compte</h2>
        <form id="register" method="post" action=$login>
            <label>Nom d'utilisateur :</label><br>
            <input type="text" name="username" required><br>
            <label>Mot de passe :</label><br>
            <input type="password" name="password" required><br><br>
            <button type="submit" name="submit" value="doLogin">Se connecter</button>
        </form>
    </div>
</div>
BODY;

    }

    /**
     * Fonction qui a pour but de creér le formulaire de modification de compte.
     * @param $username string Nom de l'utilisateur.
     * @param $email string Email de l'utilisateur.
     * @param $gravatar string Url du gravatar de l'utilisateur.
     * @return string Html generé.
     */
    private function accountChange($username, $email, $gravatar)
    {
        $editEmail = $this->app->urlFor('accountEditEmailP');
        $editPass = $this->app->urlFor('accountEditPassP');
        $str = <<<END
<div id="edit">
    <img class="gravatar" src="$gravatar" alt="gravatar"><br>
    <a class="link" href="https://fr.gravatar.com">Changer mon Gravatar</a><br>
    <label id="username">Nom d'utilisateur :</label>
    <input id="username-value" type="text" value="$username" name="username" disabled="disabled"><br>
    <br>
    <form id="email-change" method="post" action=$editEmail>
        <label>Email :</label>
        <input type="email" value="$email" name="old-email" disabled="disabled">
        <label>Nouvel email :</label>
        <input type="email" name="new-email">
        <label>Mot de passe :</label>
        <input type="password" name="password" required><br>
        <button type="submit" name="submit" value="doEmailChange">Appliquer</button>
    </form>
    <br>
    <form id="password-change" method="post" action=$editPass>
        <label>Mot de passe :</label>
        <input type="password" name="password-old" required>
        <label>Nouveau mot de passe :</label>
        <input type="password" name="password" required>
        <label>Confirmer le nouveau mot de passe :</label>
        <input type="password" name="password-confirm" required><br>
        <button type="submit" name="submit" value="doChange">Appliquer</button>
    </form>
    <br>
    <form id="delete" method="post" action="/account/edit/delete">
        <button id="delete-button" type="submit" name="submit" value="doDelete">Supprimer mon compte</button>
    </form>
</div>

END;
        return $str;
    }

    /**
     * Fonction appelée pour faire le rendu et afficher l'html generé.
     */
    public function render()
    {
        switch ($this->selecteur)
        {
            case Selection::ACCOUNT:
                $this->content = $this->accountRegisterAndLogin();
                break;
            case Selection::LOGIN_POST_FAILED:
            case Selection::REGISTER_POST_FAILED:
                $this->content = "<h3 class=\"post-code\">Une erreur lors de la récupération des données saisies</h3>";
                break;
            case Selection::REGISTER_POST_SUCCESS:
                $this->content = "<h3 class=\"post-code\">Vous êtes enregistré !</h3>";
                break;
            case Selection::REGISTER_POST_USER_OR_EMAIL_EXSITE:
                $this->content = "<h3 class=\"post-code\">L'utilisateur ou l'email est déjà enregistré</h3>";
                break;
            case Selection::LOGIN_POST_SUCCESS:
                $this->content = "<h3 class=\"post-code\">Vous êtes connecté !</h3>";
                break;
            case Selection::LOGIN_POST_USERPASS_WRONG:
                $this->content = "<h3 class=\"post-code\">L'utilisateur ou le mot de passe sont erronés</h3>";
                break;
            case Selection::LOGOUT:
                $this->content = "<h3 class=\"post-code\">Vous êtes déconnecté !</h3>";
                break;
            case Selection::CHANGE_USER:
                $this->content = $this->accountChange($this->list['username'], $this->list['email'], $this->list['gravatar']);
                break;
            case Selection::ACCOUNT_DELETE:
                $this->content = "<h3 class=\"post-code\">Compte supprimé !</h3>";
                break;
            case Selection::CHANGE_OK:
                $this->content = "<h3 class=\"post-code\">Votre compte a été mis à jour, reconnectez vous.</h3>";
                break;
            case Selection::CHANGE_EMAIL_ERROR:
                $this->content = "<h3 class=\"post-code\">Un probléme et survenue avec l'email donnée.</h3>";
                break;
            case Selection::REGISTER_TERMS_OF_USE_NOT_CHECK:
                $this->content = "<h3 class=\"post-code\">Vous devais accepte la Clauses D'utilisation.</h3>";;
                break;
            case Selection::CHANGE_BAD_PASSWORD:
                $this->content = "<h3 class=\"post-code\">le mot de passe est erronés.</h3>";
                break;
            case Selection::CHANGE_USER_ERROR:
                GlobalView::bad_request();
                return;
            default:
                GlobalView::teapot();
                return;
        }

        $body = <<<END
<div id="content">
    <div id="content-inner">
    
         $this->content
        
    </div>
</div>
END;
        ViewRendering::render($body);
    }

}