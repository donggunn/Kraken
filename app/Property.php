<?php namespace SevenShores\Kraken;

use SevenShores\Kraken\Core\Model;

class Property extends Model
{
    public function contacts()
    {
        return $this->belongsToMany(Contact::class);
    }

    public function forms()
    {
        return $this->belongsToMany(Form::class);
    }
}
