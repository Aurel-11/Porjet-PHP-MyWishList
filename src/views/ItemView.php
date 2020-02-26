<?php

namespace mywishlist\views;

use mywishlist\models\Item;
use mywishlist\models\Liste;
use mywishlist\utils\Authentication;
use mywishlist\utils\Selection;
use Slim\Slim;

/**
 * Classe ItemView, vue pour faire l'affichage de toutes les actions en relation avec les items
 * @package mywishlist\views
 */
class ItemView
{

    /**
     * @var $item mixed liste d'item, simple item ou autre, a pour but de transférer des informations
     * @var $selecteur string la fonction à appeler pour la génération de la page html
     * @var $content string variable contenant l'html généré
     * @var $app Slim variable contenant une instance de slim
     */
    protected $item, $selecteur, $content, $app;

    /**
     * ItemView constructor.
     * @param $i mixed liste d'item, simple item ou autre, a pour but de transférer des informations
     * @param $s string la fonction à appeler pour la génération de la page html
     */
    public function __construct($i, $s)
    {
        $this->item = $i;
        $this->selecteur = $s;
        $this->app = Slim::getInstance();
    }

    /**
     * Fonction appelée pour afficher les informations d'un item
     * @return string html généré
     */
    private function displayItem()
    {
        $imageUpload = $this->app->urlFor('imageUploadP', array('id' => $this->item->id));
        $itemReserve = $this->app->urlFor('reserveItemP', array('id' => $this->item->id));
        $res = "<table><th>ID</th><th>liste_ID</th><th>nom</th><th>description</th><th>tarif</th><th>Réservé par</th><th>Message de réservation</th>";
        $res .= <<<RES
<tr>
<td>{$this->item->id}</td><td>{$this->item->liste_id}</td><td>{$this->item->nom}</td><td>{$this->item->descr}</td><td>{$this->item->tarif}</td><td>{$this->item->nomReserve}</td><td>{$this->item->msgReserve}</td>
</tr>
RES;

        $res .= <<<RES
<table>
<form action=$imageUpload method="POST" enctype="multipart/form-data">
Upload d'une image pour l'item : <input type="file" name="image">
<input type="submit" name ="Uploader">
</form><br>
RES;

        $p = Item::select('nomReserve', 'msgReserve')->where('id', 'like', $this->item->id)->first();
        if($p->nomReserve == '' and $p->msgReserve == '' and Authentication::getUserId() != 0) {
            $res .= <<<END
<form action=$itemReserve method="POST" enctype="multipart/form-data">
Réservation l'item :<br>
Message de réservation : <input type="text" name="nom_reserve_item"><br>
<input type="submit" name="valider">
</form>
END;
        }
        return $res;
    }

    /**
     * Fonction appelée pour créer le formulaire de création d'item
     * @return string html généré
     */
    private function createItemForm()
    {
        $list = Liste::where('no', '=', $this->item->no)->first();
        $createItem = $this->app->urlFor('listAddItemP', array('token' => $list->token));
        $str =
            <<<END
<div id="edit">
    <h1>Création d'un item</h1>
    <form id="formCreateItem" method="POST" action=$createItem enctype="multipart/form-data">
        <input type="text" name="nom" placeholder="Nom de l'item" required>
        <input type="text" name="description" placeholder="Description de l'item" required>
        <input type="number" step="0.01" name="prix" placeholder="Prix de l'item" required>
        <input type="url" name="url" placeholder="Lien site marchand">
        <label for="image"><b>Upload d'une image pour l'item :</b></label>
        <input type="file" name="image">
        <button type="submit" name ="valid_create_item" value="valid_f1">Valider</button>
    </form>
</div>
END;
        return $str;
    }

    /**
     * Fonction qui affiche le formulaire de modification et de reservation d'un item
     * @param $manageable bool modification ou réservation
     * @return string html généré
     */
    private function manageItemForm($manageable): string
    {
        $token = Liste::where('no', '=', $this->item->liste_id)->first()['token'];
        $modifyItem = $this->app->urlFor('manageItemFromListP', array('token' =>  $token,'item' => $this->item->id));
        $itemDelete = $this->app->urlFor('deleteItemFromList', array('token' => $token, 'item' => $this->item->id));

        $itemReserve = $this->app->urlFor('reserveItemP', array('id' => $this->item->id));
        if ($manageable)
        {
            return <<<END
<h1>Modification de l'item :</h1>
<form id="formModifyItem" method="POST" action=$modifyItem enctype="multipart/form-data">
    <label for="nom"><b>Nom de l'item</b></label>
    <input type="text" name="nom" value={$this->item->nom}>
    <label for="description"><b>Description de l'item</b></label>
    <input type="text" name="description" value={$this->item->descr}>
    <label for="prix"><b>Prix de l'item</b></label>
    <input type="number" step="0.01" name="prix" value={$this->item->tarif}>
    <label for="url"><b>Lien site marchand</b></label>
    <input type="url" name="url" value={$this->item->url}>
    <label for="image"><b>Upload d'une image pour l'item :</b></label>
    <input type="file" name="image">
    <button type="submit" name ="valid_modify_item" value="valid_f1">Valider</button>
</form>
<button id="delete-button" onclick="window.location.href = '$itemDelete';" type="button" name="submit">Supprimer l'item</button>
END;
        } else {
            $p = Item::select('nomReserve', 'msgReserve')->where('id', 'like', $this->item->id)->first();
            $createurID = Liste::select('user_id')->where('no', '=', $this->item->liste_id)->first();
            if($p->nomReserve == '' and $p->msgReserve == '' and Authentication::getUserId() != 0 and Authentication::getUserId() != $createurID->user_id) {
                return <<<END
<form id="formModifyItem" action=$itemReserve method="POST">
<h1>Réservation l'item :</h1>
<label for ="Message de réservation : "><b>Message de réservation : </b></label>
<input type="text" name="nom_reserve_item"><br>
<button type="submit" name ="valid_reserve_item" value="valid_res">Valider</button>
</form>
END;
            }
        }
        GlobalView::forbidden();
        exit();
    }

    /**
     * Fonction appeléé pour faire le rendu de la page web
     */
    public function render()
    {
        switch ($this->selecteur)
        {
            case Selection::FORM_CREATE_ITEM:
                $this->content = $this->createItemForm();
                break;
            case Selection::FORM_MODIFY_ITEM_MANAGE:
                $this->content = $this->manageItemForm(true);
                break;
            case Selection::FORM_MODIFY_ITEM_PART:
                $this->content = $this->manageItemForm(false);
                break;
            case Selection::ID_ITEM:
                $this->content = $this->displayItem();
                break;
            case Selection::FORM_ITEM_RESERVE_FAIL:
                $this->content = "<p> Une erreur est survenu, vérifiez que l'item n'est pas déjà réservé.</p>";
                break;
            case Selection::FORM_ITEM_RESERVE_SUCCESS:
                $this->content = "<p> Item réservé avec succès !</p>";
                break;
            case Selection::FORM_IMAGE_UPLOAD_FAIL:
                $this->content = "<p>Une erreur a eu lieu lors de l'upload de l'image</p>";
                break;
            case Selection::FORM_IMAGE_UPLOAD_SUCCESS:
                $this->content = "<p>Image upload avec succès</p>";
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