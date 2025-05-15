<?php

namespace App\Enum;

enum JobStatus: string
{
    case cdi = "CDI";
    case cdd = "CDD";
    case freelance = "Freelance";
    case interim = "Interim";

}