<?php

namespace mywishlist\utils;

use mywishlist\models\User;

/**
 * Classe Authentication, classe appelée pour tout ce qui concerne l'utilisateur
 * @package mywishlist\utils
 */
class Authentication
{
    /**
     * Toutes les constantes corespondantes au niveau d'action des utilisateurs
     */
    const ANONYMOUS  = 0; // default user id & user level
    const USER = 1; // user level by default
    const ADMIN = 2;
    const SUPER_ADMIN = 3;

    /**
     * Fonction qui permet d'obtenir le niveau d'action de l'utilisateur
     * @return int niveau d'action
     */
    public static function getUserLevel(): int
    {
        if (isset($_SESSION['user_id']))
        {
            if (isset(User::select('user_level')->where('user_id', '=', $_SESSION['user_id'])->get()[0]))
            {
                return User::select('user_level')->where('user_id', '=', $_SESSION['user_id'])->get()[0]['user_level'];
            } else {
                $_SESSION['user_id'] = self::ANONYMOUS;
                return self::ANONYMOUS;
            }
        } else {
            $_SESSION['user_id'] = self::ANONYMOUS;
            return self::ANONYMOUS;
        }
    }

    /**
     * Fonction qui permet d'avoir le nom d'utilisateur associé à l'utilsateur
     * @return string nom d'utilisateur associé à l'utilsateur
     */
    public static function getUsername()
    {
        if (self::getUserLevel() != self::ANONYMOUS)
        {
            return User::select('username')->where('user_id', '=', $_SESSION['user_id'])->first()->username;
        } else {
            return 'anonymous';
        }
    }

    /**
     * Fonction qui permet d'avoir l'id associé à l'utilisateur
     * @return int|mixed id de l'utisateur
     */
    public static function getUserId()
    {
        if (self::getUserLevel() != self::ANONYMOUS)
        {
            return $_SESSION['user_id'];
        } else {
            return 0;
        }
    }

    /**
     * Fonction utilisée pour obtenir l'email de l'utisateur
     * @return string email de l'utilisateur
     */
    public static function getEmail()
    {
        if (self::getUserLevel() != self::ANONYMOUS)
        {
            return User::select('email')->where('user_id', '=', $_SESSION['user_id'])->first()->email;
        } else {
            return '';
        }
    }
}