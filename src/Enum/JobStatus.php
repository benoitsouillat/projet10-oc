<?php

namespace App\Enum;

enum JobStatus: string
{
    case toDefine = "A définir";
    case cdi = "CDI";
    case cdd = "CDD";
    case freelance = "Freelance";
    case interim = "Interim";

    public function label(): string
    {
        return match($this) {
            self::toDefine => 'A définir',
            self::cdi => 'CDI',
            self::cdd => 'CDD',
            self::freelance => 'Freelance',
            self::interim => 'Interim',
        };
    }

}