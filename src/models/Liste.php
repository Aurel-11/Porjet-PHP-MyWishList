<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Classe Liste, modèle utilisé pour intéragir avec la base de données
 * @package mywishlist\models
 */
class Liste extends Model
{

    /**
     * @var string nom de la table dans la bdd
     */
    protected $table = 'liste';

    /**
     * @var string nom de la clé primaire dans la bdd
     */
    protected $primaryKey = 'no';

    /**
     * @var bool gestion du temps
     */
    public $timestamps = false;

    /**
     * Fonction utilisée pour les relations avec les autres modèles
     * @return Model|BelongsTo|object|null
     */
    public function no()
    {
        return $this->belongsTo('\models\Item', 'liste_id')->first();
    }
}