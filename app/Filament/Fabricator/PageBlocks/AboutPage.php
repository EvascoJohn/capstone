<?php

namespace App\Filament\Fabricator\PageBlocks;


use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class AboutPage extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('about-page')
            ->schema([
                //HERO BLADE TITLE:     TEXT/Text input
                Forms\Components\TextInput::make('hero_description')
                        ->label('Hero title')
                        ->helperText(config('customer-website.about-page-block.hero_helper_text')),
                //HOME PAGE HERO IMAGE IMAGE
                Forms\Components\FileUpload::make("heading_image")
                        ->required()
                        ->label("Heading Image")
                        ->disk('public')
                        ->directory('site-images')
                        ->visibility('private')
                        ->acceptedFileTypes(['image/jpeg'])
                        ->getUploadedFileNameForStorageUsing(
                            fn (Forms\Get $get, TemporaryUploadedFile $file): string => (string) "about-page-hero-image.jpg"
                        ),
                //BRAND DESCRIPTION TEXT  TEXT/Text input
                Forms\Components\MarkdownEditor::make('brand_description_text')
                        ->label('Brand Description')
                        ->helperText(config('customer-website.about-page-block.brand_description_helper_text')),
                //Inquire BUTTON:      TEXT/Text input
                Forms\Components\MarkdownEditor::make('vision_text')
                        ->label('Vision')
                        ->helperText(config('customer-website.about-page-block.vision_helper_text')),
                //EXPLORE               TEXT/Text input
                Forms\Components\MarkdownEditor::make('mission_text')
                        ->label('Mission')
                        ->helperText(config('customer-website.about-page-block.mission_helper_text')),
                //LATEST PRODUCTS TEXT  TEXT/Text input
                Forms\Components\TextInput::make('read_more_button_text')
                        ->helperText(config('customer-website.about-page-block.readmore_button_helper_text'))
                        ->label('Read more Button Text'),
                    ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}