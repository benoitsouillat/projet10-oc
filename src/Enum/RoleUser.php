<?php

namespace App\Enum;

enum RoleUser: string
{
    case Administrateur = "ROLE_ADMIN";
    case ChefDeProjet = "ROLE_PROJECT_OWNER";
    case Collaborateur= "ROLE_USER";

    public function label(): string
    {
        return match($this) {
            self::Administrateur => 'Administrateur',
            self::ChefDeProjet => 'Chef de projet',
            self::Collaborateur => 'Collaborateur',
        };
    }
}