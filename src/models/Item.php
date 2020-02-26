<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Classe Item, modèle utilisé pour intéragir avec la base de données
 * @package mywishlist\models
 */
class Item extends Model
{
    /**
     * @var string nom de la table dans la bdd
     */
    protected $table = 'item';

    /**
     * @var string nom de la clé primaire dans la bdd
     */
    protected $primaryKey = 'id';

    /**
     * @var bool gestion du temps
     */
    public $timestamps = false;

    /**
     * Fonction utiliséd pour les clés étrangères
     * @return Collection listes des clés étrangères
     */
    public function liste_id()
    {
        return $this->hasMany('\models\Liste', 'no')->get();
    }
}