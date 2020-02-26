<?php

namespace mywishlist\views;

use DateTime;
use Exception;
use mywishlist\utils\Selection;
use Slim\Slim;

/**
 * Classe ListView, vue qui a pour but de générer l'affichage avec tout se qui est en rapport avec les listes
 * @package mywishlist\views
 */
class ListView
{

    /**
     * @var $list mixed liste de liste, simple liste ou autre, a pour but de transférer des informations
     * @var $selecteur string la fonction à appeler pour la génération de la page html
     * @var $content string variable contenant l'html généré
     * @var $app Slim variable contenant un instance de slim
     */
    protected $list, $selecteur, $content, $app;

    /**
     * ItemView constructor.
     * @param $l mixed liste de liste, liste simple ou autre, a pour but de transférer des informations
     * @param $s string la fonction à appeler pour la génération de la page html
     */
    public function  __construct($l, $s)
    {
        $this->list = $l;
        $this->selecteur = $s;
        $this->app = Slim::getInstance();
    }

    /**
     * Fonction qui a pour but de générer une partie de l'affichage, dans ce cas affichage des listes
     * @param $array array liste des valeurs à afficher
     * @param bool $edit condition du lien d'accès
     * @return string html généré
     */
    private function buildListTable($array, bool $edit)
    {
        $out = <<<END
<table>
    <tr>
        <th>titre</th>
        <th>description</th>
        <th>expiration</th>
    </tr>
END;
        foreach ($array as $values)
        {
            if ($edit) $link = $this->app->urlFor('list', array('token' => $values->token));
            else $link = $this->app->urlFor('list', array('token' => $values->tokenPart));
            $out .= <<<END
    <tr>
        <td><a class="link" href=$link>$values->titre</a></td>
        <td>$values->description</td>
        <td>$values->expiration</td>
    </tr>
END;
        }
        return $out . '</table>';
    }

    /**
     * Fonction qui a pour but d'afficher toutes les listes que l'utilisateur possède ou auxquelles il participe
     * @return string html généré
     */
    private function displayMyLists()
    {
        $res = '<div id="myLists"><h1>Mes listes</h1>';
        $res .= $this->buildListTable($this->list['myLists'], true);
        $res .= "<button type=\"button\" onclick=\"window.location.href = '/list/create';\" value=\"goToCreateList\">Créer un nouvelle liste</button>";
        $res .= '</div>';
        $res .= '<div id="listsByOthers"><h1>Listes ou je participe</h1>';
        $res .= $this->buildListTable($this->list['participLists'], false);
        $res .= '</div>';
        return $res;
    }

    /**
     * Fonction qui a pour but d'afficher toutes les listes publiques
     * @return string html généré
     */
    private function displayPublicList()
    {
        $res = '<div id="listsByOthers"><h1>listes public</h1>';
        $res .= $this->buildListTable($this->list, false);
        $res .= '</div>';
        return $res;
    }

    /**
     * Fonction qui a pour but de générer l'affichage des items d'une liste
     * @param $item array liste d'items
     * @param $args array argument d'affichage
     * @return string html généré
     */
    private function buildItemList($item, $args): string
    {
        $targetDir = "/uploads";
        if (!$args['exp'] && !$args['p']) $out = "<table><tr><th>Image</th><th>Nom</th><th>Statut de la réservation</th>";
        else $out = "<table><tr><th>Image</th><th>Nom</th><th>Réservation par</th><th>Message</th>";
        if (!$args['p']) $out .= '<th>Modifier l\'item</th></tr>';
        else $out .= '<th>Réserver l\'item</th></tr>';

        foreach ($item as $key => $value)
        {
            $url = $this->app->urlFor('manageItemFromList', array('token' => $args['token'], 'item' => $value->id));
            $editable = (!empty($value->nomReserve) || $args['exp']) ? 'disabled' : '';
            if (!$args['exp'] && !$args['p'])
            {
                if (!empty($value->nomReserve)) $resv = "Reservé";
                else $resv = "Non réservé";
                $out .= "<tr><td><img src=\"$targetDir/$value->img\" alt=\"$value->img\" class='img'></td><td>$value->nom</td><td>$resv</td>";
                $out .= "<td><button type='button' onclick=\"window.location.href = '$url';\" value=\"goToCreateList\" $editable>Modifier l'item</button></td>";
                $out .= "</tr>";
            } else {
                $out .= "<tr><td><img src=\"$value->img\" alt=\"$value->img\" class='img'></td><td>$value->nom</td><td>$value->nomReserve</td><td>$value->msgReserve</td>";
                $out .= "<td><button type='button' onclick=\"window.location.href = '$url';\" value=\"goToCreateList\" $editable>Réserver l'item</button></td>";
                $out .= "</tr>";
            }
        }
        return $out . "</table>";
    }

    /**
     * Fonction qui affiche un liste en details
     * @param $modifiable bool les permissions de l'utilisateur pour pouvoir adapter l'affichage
     * @return string html généré
     * @throws Exception liée à la date d'expiration
     */
    private function displayListContent($modifiable)
    {
        $res = '<div id="authors">';
        $i = 0;
        foreach ($this->list['authors'] as $u)
        {
            if ($i != 0) $res .= '<br>';
            $i++;
            $gravatar = $u['gravatar'];
            $username = $u['username'];
            $owner = '';
            if ($u['owner'] == true)
            {
                $owner = ' owner';
            }
            $res .= <<<END
<img alt="$username" class="gravatar$owner" src="$gravatar"><br><label>$username</label>
END;
        }

        $res .= '</div>';

        $res .= '<div id="items">';
        $title = $this->list['title'];
        $desc = $this->list['desc'];
        $exp = $this->list['exp'];
        $tokPart = $this->list['tokenPart'];
        $res .= "<h1>$title</h1><p>$desc</p><p>$exp</p>";

        if ($modifiable)
        {

            $urlEdit = $this->app->urlFor('listMod', array('token' => $this->list['token']));

            $res .= "<button type=\"button\" onclick=\"window.location.href = '$urlEdit'\" value=\"goToShareList\">Modifier la liste</button>";

            if(empty($tokPart))
            {
                $urlShare = $this->app->urlFor('listShare', array('token' => $this->list['token']));
                $res .= "<button type=\"button\" onclick=\"window.location.href = '$urlShare'\" value=\"goToShareList\">Generé un lien de partage</button>";
            } else {
                $urlShare = $_SERVER['SERVER_NAME'] . $this->app->urlFor('list', array('token' => $this->list['tokenPart']));
                $res .= <<<SHARE
<input type="text" value="$urlShare" id="share">

<div class="tooltip">
    <button type="button" onclick="copy()" onmouseout="copy_post()">
      <span id="tooltip_text">Copier dans le press-papier</span>
      Copié le lien
    </button>
</div>

<script>
function copy() {
  const copyText = document.getElementById("share");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  document.execCommand("copy");
  
  const tooltip = document.getElementById("tooltip_text");
  tooltip.innerHTML = "Copié: " + copyText.value;
}

function copy_post() {
  const tooltip = document.getElementById("tooltip_text");
  tooltip.innerHTML = "Copié dans le pres-papier";
}
</script>
SHARE;
            }
        }

        $exp = DateTime::createFromFormat('Y-m-d', $exp);
        $now = new DateTime('now');
        if ($modifiable)
        {
            if ($exp >= $now) $array = array('p' => false, 'exp' => false, 'token' => $this->list['token']);
            else $array = array('p' => false, 'exp' => true, 'token' => $this->list['token']);
        } else {
            if ($exp >= $now) $array = array('p' => true, 'exp' => false, 'token' => $this->list['token']);
            else $array = array('p' => true, 'exp' => true, 'token' => $this->list['token']);
        }

        $res .= $this->buildItemList($this->list['items'], $array);

        if ($modifiable && $exp >= $now)
        {
            $url = $this->app->urlFor('listAddItem', array('token' => $this->list['token']));
            $res .= "<button type=\"button\" onclick=\"window.location.href = '$url'\" value=\"goToShareList\">Ajouter un item</button>";
        }

        return $res . "</div>";
    }

    /**
     * Fonction qui affiche le formulaire de création de liste
     * @return string html généré
     */
    private function formCreateList()
    {
        $createList = $this->app->urlFor('listCreateP');
        $str =
            <<<END
<div id="edit">
    <h1>Creation d'une liste</h1>
    <form id="formCreateList" method="POST" action=$createList>
        <input type="text" name="titre" placeholder="Titre de la liste" required>
        <input type="text" name="description" placeholder="Description de la liste" required>
        <input type="date" name="date" placeholder="Date d'expiration de la liste" required>
        <input type="checkbox" name="public" value="oui" id="checkbox" class="css-checkbox"/>
			<label for="checkbox" class="css-label">Rendre le liste publique ? </label><br>
        <button type="submit" name ="valid_create_list" value="valid_f1">Valider</button>
    </form>
</div>
END;
        return $str;
    }

    /**
     * Fonction qui permmet d'afficher le formulaire de modification de liste
     * @return string html généré
     */
    private function formModifyList()
    {
        $modifyList = $this->app->urlFor('listModP', array('id' => $this->list->no));
        return <<<END
<div id="edit">
    <h1>Modification d'une liste</h1>
    <form id="formModifyList" method="POST" action=$modifyList>
        <input type="text" name="titre" placeholder="Titre de la liste" required>
        <input type="text" name="description" placeholder="Description de la liste" required>
        <input type="date" name="date" placeholder="Date d'expiration de la liste" required>
        <input type="checkbox" name="public" value="oui" id="checkbox" class="css-checkbox"/>
			<label for="checkbox" class="css-label">Rendre le liste publique ? </label><br>
        <button type="submit" name ="valid_modify_list" value="valid_f1">Valider</button>
    </form>
</div>

END;
    }

    /**
     * Fonction appélee pour faire le rendu
     * @throws Exception liée aux exceptions précédemment obtenue
     */
    public function render()
    {

        switch ($this->selecteur)
        {
            case Selection::ALL_LIST:
                $this->content = $this->displayMyLists();
                break;
            case Selection::TOKEN_LIST_MODIFIABLE:
                $this->content = $this->displayListContent(true);
                break;
            case Selection::TOKEN_LIST:
                $this->content = $this->displayListContent(false);
                break;
            case Selection::LIST_PUBLIC:
                $this->content = $this->displayPublicList();
                break;
            case Selection::FORM_CREATE_LIST:
                $this->content = $this->formCreateList();
                break;
            case Selection::FORM_MODIFY_LIST:
                $this->content = $this->formModifyList();
                break;
            default:
                $this->content = "Switch Constant Error";
                break;
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
