<?php

namespace App\Filament\Fabricator\PageBlocks;

use App\Models\UnitModel;
use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class ProductPage extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('product-page')
            ->schema([
                    //HERO BLADE TITLE:     TEXT/Text input
                    Forms\Components\TextInput::make('hero_title')
                            ->label('Hero title')
                            ->helperText(config('customer-website.product-page-block.hero_helper_text')),
                    //HOME PAGE HERO IMAGE IMAGE
                    Forms\Components\FileUpload::make("heading_image")
                            ->required()
                            ->label("Heading Image")
                            ->disk('public')
                            ->directory('site-images')
                            ->visibility('private')
                            ->acceptedFileTypes(['image/jpeg'])
                            ->getUploadedFileNameForStorageUsing(
                                fn (Forms\Get $get, TemporaryUploadedFile $file): string => (string) "productpage-hero-image.jpg"
                            ),
                    //Inquire BUTTON:      TEXT/Text input
                    Forms\Components\TextInput::make('inquire_button')
                            ->label('Inquire button')
                            ->helperText(config('customer-website.product-page-block.inquire_button_helper_text')),
                    //EXPLORE               TEXT/Text input
                    Forms\Components\TextInput::make('explore_button')
                            ->label('Explore button')
                            ->helperText(config('customer-website.product-page-block.explore_button_helper_text')),
                    //LATEST PRODUCTS TEXT  TEXT/Text input
                    Forms\Components\TextInput::make('latest_products')
                            ->helperText(config('customer-website.product-page-block.products_helper_text'))
                            ->label('Latest Products'),
            ]);
    }

    public static function mutateData(array $data): array
    {
        $data['product_list'] = UnitModel::query()->take(10)->get();
        return $data;
    }
}