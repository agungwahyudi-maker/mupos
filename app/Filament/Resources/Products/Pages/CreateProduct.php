<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    //autoredirect ke halaman list setelah create
    protected function getRedirectUrl(): string
    {        
        return $this->getResource()::getUrl('index');
    }
}
