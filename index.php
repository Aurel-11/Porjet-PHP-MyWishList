<?php

// Import de la classe qui a pour but de charger tous les imports du projet
require_once './vendor/autoload.php';

// Liste de tous les imports
use \mywishlist\controllers\UserController;
use \mywishlist\controllers\ListController;
use \mywishlist\controllers\ItemController;
use \mywishlist\views\AccueilView;
use \Illuminate\Database\Capsule\Manager as DB;
use mywishlist\views\GlobalView;
use \Slim\Slim;

// instance de la base de données
$db = new DB();

// ajout des informations pour se connecter à la base de données
$ini_file = parse_ini_file('src/conf/conf.ini');
$db->addConnection([
    'driver'    => $ini_file['driver'],
    'host'      => $ini_file['host'],
    'database'  => $ini_file['database'],
    'username'  => $ini_file['username'],
    'password'  => $ini_file['password'],
    'charset'   => $ini_file['charset'],
    'collation' => $ini_file['charset'] . '_unicode_ci',
    'prefix'    => ''
]);

// démarrage de la base de données
$db->setAsGlobal();
$db->bootEloquent();

// démarrage d'une session
session_start();

// instance de slim qui a pour but de créer le rootage des urls
$app = new Slim();


/*-----|accueil|-----*/
$app->get('/', function () {
    $v = new AccueilView();
    $v->render();
})->name('accueil');


/*-----|listes|-----*/

$app->get('/list/public', function () {
    $c = new ListController();
    $c->listPublic();
})->name('listPublic');

$app->get('/list/create', function () {
    $c = new ListController();
    $c->createListForm();
})->name('listcreate');

$app->get('/list/:token', function ($token) {
    $c = new ListController();
    $c->showListContent($token);
})->name('list');

$app->get('/list/:token/modify', function ($token) {
    $c = new ListController();
    $c->modifyListForm($token);
})->name('listMod');

$app->post('/list/:token/modify/submit', function ($token) {
    $c = new ListController();
    $c->modifyList($token);
})->name('listModP');

$app->post('/list/create/submit', function () {
    $c = new ListController();
    $c->createList();
})->name('listCreateP');

$app->get('/list/:token/addItem', function ($token) {
    $c = new ItemController();
    $c->createItemForm($token);
})->name('listAddItem');

$app->post('/list/:token/addItem/submit', function ($token) {
    $c = new ItemController();
    $c->createItem($token);
})->name('listAddItemP');

$app->get('/list/:token/share', function ($token) {
    $c = new ListController();
    $c->share($token);
})->name('listShare');



/*-----|items|-----*/
$app->get('/list/:token/:item/manage', function ($token, $item) {
    $c = new ItemController();
    $c->manageItemForm($token, $item);
})->name('manageItemFromList');

$app->post('/list/:token/:item/manage/submit', function ($token, $item) {
    $c = new ItemController();
    $c->modifyItem($token, $item);
})->name('manageItemFromListP');

$app->get('/list/:token/:item/delete', function ($token, $item) {
    $c = new ItemController();
    $c->deleteItem($token, $item);
})->name('deleteItemFromList');

$app->get('/item/:id', function ($id) {
    $c = new ItemController();
    $c->showItemInfo($id);
})->name('item');


$app->post('/item/reserve/submit/:id', function ($id) {
    $c = new ItemController();
    $c->reserveItemSubmit($id);
})->name('reserveItemP');

$app->post('/item/upload/submit/:id', function ($id) {
    $c = new ItemController();
    $c->ajoutImage($id);
})->name('imageUploadP');


/*-----|comptes|-----*/
$app->get('/account', function () {
    $c = new UserController();
    $c->account();
})->name('account');

$app->get('/account/mylists', function () {
    $c = new ListController();
    $c->showMyList();
})->name('accountLists');

$app->get('/account/edit', function () {
    $c = new UserController();
    $c->accountEdit();
})->name('accountEdit');

$app->post('/account/edit/password', function () {
    $c = new UserController();
    $c->changePassword();
})->name('accountEditPassP');

$app->post('/account/edit/email', function () {
    $c = new UserController();
    $c->accountEmail();
})->name('accountEditEmailP');

$app->post('/account/edit/delete', function () {
    $c = new UserController();
    $c->accountDelete();
})->name('accountDelP');

$app->get('/account/logout', function () {
    $c = new UserController();
    $c->logout();
})->name('accountLogout');

$app->post('/account/register_post', function () {
    $c = new UserController();
    $c->registerPost();
})->name('accountRegisterP');

$app->post('/account/login_post', function () {
    $c = new UserController();
    $c->loginPost();
})->name('accountLoginP');

$app->get('/account/teapot', function () {
    GlobalView::teapot();
})->name('teapot');

$app->run();