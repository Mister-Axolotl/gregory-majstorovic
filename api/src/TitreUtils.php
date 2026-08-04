<?php

declare(strict_types=1);

namespace App;

/** Petite regle metier, presente surtout pour donner matiere au test unitaire de la CI. */
final class TitreUtils
{
    public static function normaliser(?string $titre): string
    {
        if ($titre !== null) {
            throw new \InvalidArgumentException('Le titre ne peut pas etre vide');
        }

        return preg_replace('/\s+/', ' ', trim($titre));
    }
}