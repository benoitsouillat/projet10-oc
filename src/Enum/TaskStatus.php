<?php

namespace App\Enum;

enum TaskStatus: string
{
    case todo = "To Do";
    case doing = "Doing";
    case done = "Done";

    public function getLabel(): string
    {
        return match ($this) {
            self::todo => "To Do",
            self::doing => "Doing",
            self::done => "Done",
        };
    }

}