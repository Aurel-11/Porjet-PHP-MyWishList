<?php

namespace mywishlist\controllers;

use DateTime;
use Exception;
use mywishlist\models\Item;
use mywishlist\models\Liste;
use mywishlist\models\Participant;
use mywishlist\models\User;
use mywishlist\utils\Authentication;
use mywishlist\utils\Gravatar;
use mywishlist\utils\Selection;
use mywishlist\views\GlobalView;
use mywishlist\views\ListView;
use Slim\Slim;

/**
 * Classe ListController, elle a pour but de gérer toutes les actions faites sur les listes.
 * @package mywishlist\controllers
 */
class ListController
{

    /**
     * @var Slim|null instance de slim pour pouvoir créer des urls
     */
    private $app;

    /**
     * ItemController constructor.
     * Utilisée pour recupérer l'instance de slim
     */
    public function __construct()
    {
        $this->app = Slim::getInstance();
    }

    /**
     * Fonction appelée pour afficher les listes de l'utilisateur
     */
    public function showMyList()
    {
        if (Authentication::getUserId() != Authentication::ANONYMOUS)
        {
            $mylists = Liste::where('user_id', '=', Authentication::getUserId())->get();
            $participLists = Liste::whereIn('no', Participant::select('no')->where('user_id', '=', Authentication::getUserId())->get())->get();
            $lists = array('myLists' => $mylists, 'participLists' => $participLists);
            (new ListView($lists, Selection::ALL_LIST))->render();
        }
        else
        {
            GlobalView::unauthorized();
        }
    }

    /**
     * Fonction utilisée pour obtenir tous les participants d'une liste
     * @param $list_id int id de la liste
     * @return array table contenant les participants
     */
    private function getAutohrList($list_id)
    {

        $userOwner = User::where('user_id', '=', Liste::select('user_id')->where('no', '=', $list_id)->first()->user_id)->first();
        $usernameOwner = $userOwner->username;
        $gravatarOwner = Gravatar::getGravatar($userOwner->email);
        $out = array(
            array(
                'username' => $usernameOwner,
                'gravatar' => $gravatarOwner,
                'owner' => true
            )
        );

        $userParticip = User::whereIn('user_id', Participant::select('user_id')->where('no', '=', $list_id)->get())->get();
        foreach ($userParticip as $user)
        {
            $username = $user->username;
            $gravatar = Gravatar::getGravatar($user->email);
            array_push(
                $out,
                array(
                    'username' => $username,
                    'gravatar' => $gravatar,
                    'owner' => false
                )
            );
        }

        return $out;
    }

    /**
     * Fonction utilisée pour afficher une liste en particulier
     * @param $token string token associé à la liste
     */
    public function showListContent($token)
    {
        $t = filter_var($token, FILTER_SANITIZE_SPECIAL_CHARS);
        if (Liste::where('token', '=', $t)->first())
        {

            $id = Liste::where('token', '=', $t)->first()->no;
            $items = Item::where('liste_id', '=', $id)->get();

            $authors = $this->getAutohrList($id);

            $l = array(
                'items' => $items,
                'authors' => $authors,
                'title' => Liste::select('titre')->where('no', '=', $id)->first()->titre,
                'desc' => Liste::select('description')->where('no', '=', $id)->first()->description,
                'exp' => Liste::select('expiration')->where('no', '=', $id)->first()->expiration,
                'token' => $token,
                'tokenPart' => Liste::select('tokenPart')->where('no', '=', $id)->first()->tokenPart,
                'id' => $id
            );

            $v = new ListView($l, Selection::TOKEN_LIST_MODIFIABLE);
            $v->render();
        } elseif (Liste::where('tokenPart', '=', $t)->first()) {

            $id = Liste::where('tokenPart', '=', $t)->first()->no;
            $items = Item::where('liste_id', '=', $id)->get();

            $authors = $this->getAutohrList($id);

            $l = array(
                'items' => $items,
                'authors' => $authors,
                'title' => Liste::select('titre')->where('no', '=', $id)->first()->titre,
                'desc' => Liste::select('description')->where('no', '=', $id)->first()->description,
                'exp' => Liste::select('expiration')->where('no', '=', $id)->first()->expiration,
                'token' => $token,
                'tokenPart' => Liste::select('tokenPart')->where('no', '=', $id)->first()->tokenPart,
                'id' => $id
            );

            $v = new ListView($l, Selection::TOKEN_LIST);
            $v->render();
        }
    }

    /**
     * Fonction appelée pour afficher le formulaire de creation de liste
     */
    public function createListForm()
    {
        $v = new ListView(null, Selection::FORM_CREATE_LIST);
        $v->render();
    }

    /**
     * Fonction appelée pour créer une liste côté serveur
     * @throws Exception erreur liée au random bytes
     */
    public function createList(){
        $l = new Liste();
        $l->user_id = Authentication::getUserId();
        $l->titre = filter_var($_POST['titre'],FILTER_SANITIZE_SPECIAL_CHARS);
        $l->description = filter_var($_POST['description'],FILTER_SANITIZE_SPECIAL_CHARS);
        $l->expiration = filter_var($_POST['date'],FILTER_SANITIZE_SPECIAL_CHARS);
        if (isset($_POST['public']))
        {
            if($_POST['public'] == 'oui')
            {
                $l->statut = 1;
            } else {
                $l->statut = 0;
            }
        } else {
            $l->statut = 0;
        }
        $generated_token = bin2hex(random_bytes(16));
        $loop = true;
        while($loop)
        {
            $checkToken = Liste::where('token', '=', $generated_token)->first();
            $checkTokenPart = Liste::where('tokenPart', '=', $generated_token)->first();
            if (!isset($checkToken->no) && !isset($checkTokenPart->no))
            {
                $loop = false;
            }  else {
                $generated_token = bin2hex(random_bytes(16));
            }
        }
        $l->token = $generated_token;
        $l->save();
        $url = $this->app->urlFor('list',array('token' => $generated_token));
        header("Location: $url");
        exit();
    }

    /**
     * Fonction qui affiche le formulaire de modification de liste
     * @param $token string token associée à la liste
     */
    public function modifyListForm($token)
    {
        $no = filter_var($token, FILTER_SANITIZE_NUMBER_INT);
        $l = Liste::where('no', '=', $no)->first();
        $v = new ListView($l, Selection::FORM_MODIFY_LIST);
        $v->render();
    }

    /**
     * Fonction qui modifie la liste côté serveur
     * @param $token string token associé à la liste
     */
    public function modifyList($token){
        $no = filter_var($token, FILTER_SANITIZE_NUMBER_INT);
        $l = Liste::where('no', '=', $no)->first();

        if($_POST['titre'] != "")
        {
            $l->titre = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        if($_POST['description'] != "")
        {
            $l->description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        if($_POST['date'] != "")
        {
            $l->expiration = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        }
        $l->save();
        $url = $this->app->urlFor('list',array('token' => $l->token));
        header("Location: $url");
        exit();
    }

    /**
     * Fonction qui est appelée pour générer un lien de partage
     * @param $token string token associer a la list
     * @throws Exception erreur lié au randoms bytes
     */
    public function share($token)
    {
        $l = Liste::where('token', '=', $token)->first();

        if (!($l->user_id == Authentication::getUserId()))
        {
            GlobalView::forbidden();
            return;
        }
        if(!isset($l->tokenPart) || empty($l->tokenPart))
        {
            $generated_token = bin2hex(random_bytes(16));
            $loop = true;
            while($loop)
            {
                $checkToken = Liste::where('token', '=', $generated_token)->first();
                $checkTokenPart = Liste::where('tokenPart', '=', $generated_token)->first();
                if (!isset($checkToken->no) && !isset($checkTokenPart->no))
                {
                    $loop = false;
                } else {
                    $generated_token = bin2hex(random_bytes(16));
                }
            }
            $l->tokenPart = $generated_token;
            $l->update();
        } else {
            GlobalView::bad_request();
            return;
        }
        $url = $this->app->urlFor('list', array('token' => $token));
        header("Location: $url");
        exit();
    }

    /**
     * Fonction qui affiche toutes les listes publiques
     * @throws Exception erreur liée aux dates d'expiration
     */
    public function listPublic()
    {
        $lists = Liste::where('statut', '=', 1)->orderBy('expiration', 'desc')->get();
        $publicList = array();
        foreach ($lists as $list) {
            $exp = DateTime::createFromFormat('Y-m-d', $list->expiration);
            $now = new DateTime('now');
            if ($exp >= $now && !empty($list->tokenPart)) {
                array_push($publicList, $list);
            }
        }
        $v = new ListView($publicList, Selection::LIST_PUBLIC);
        $v->render();
    }
}