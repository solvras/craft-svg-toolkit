<?php

namespace solvras\craftsvgtoolkit\models;

use craft\base\Model;

/**
 * svg-toolkit settings
 */
class Settings extends Model
{
    public array $paths = [];

    public function rules(): array
    {
        return [
            [['paths'], 'required'],
            [['paths'], 'array'],
        ];
    }
}
