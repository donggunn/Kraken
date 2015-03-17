<?php namespace SevenShores\Kraken\Repositories;

use SevenShores\Kraken\Contact;
use SevenShores\Kraken\Contracts\Repository;
use SevenShores\Kraken\Core\EloquentRepository;

class Contacts extends EloquentRepository implements Repository
{
    protected $model = Contact::class;
}
