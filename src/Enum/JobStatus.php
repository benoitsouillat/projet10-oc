<?php

namespace App\Enum;

enum JobStatus: string
{
    case toDefine = "A définir";
    case cdi = "CDI";
    case cdd = "CDD";
    case freelance = "Freelance";
    case interim = "Interim";

}