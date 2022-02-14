<?php

namespace App\Services;

class Censurator
{
    const MOTS_INTERDITS = ["putain", "foutre", "merde", "chier","con ", "pute", "bordel","enfoiré","salope", "salaud", "pétasse","pouffiasse", "casse-couille"];

    /**
     * Purify
     *
     * Méthode permettant du remplacer les mots interdits par des étoiles
     *
     * @param string $texte la phrase brut
     * @return string la phrase purifiée
     */
    public function purify(string $texte): string
    {
        foreach (self::MOTS_INTERDITS as $motInterdit) {
            // Je remplace le mot interdit par des etoiles
            $remplacement = str_repeat("*", mb_strlen($motInterdit));
            // Je cherche le mot interdit dans la phrase et je le remplace
            $texte = str_ireplace($motInterdit, $remplacement, $texte);
        }
        return $texte;
    }
}