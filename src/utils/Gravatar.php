<?php


namespace mywishlist\utils;

/**
 * Classe Gravatar
 * @package mywishlist\utils
 */
class Gravatar
{
    /**
     * Implémantation des gravatars.
     * @param $email string Email de l'utilisateur.
     * @param int $s int Taille de l'image (Par defaut 256 pixel de côté).
     * @return string Url du gravatar.
     */
    public static function getGravatar($email, $s = 256) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=retro&r=g";
        return $url;
    }
}