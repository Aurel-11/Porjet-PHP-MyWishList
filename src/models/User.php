<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Classe User, modele utilisé pour intéragir avec la base de données
 * @package mywishlist\models
 */
class User extends Model
{
    /**
     * @var string nom de la table dans la bdd
     */
    protected $table = 'user';

    /**
     * @var string nom de la clé primaire dans la bdd
     */
    protected $primaryKey = 'user_id';

    /**
     * @var bool gestion du temps
     */
    public $timestamps = false;
}