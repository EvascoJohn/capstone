<?php

namespace App\Filament\Fabricator\PageBlocks;

use Filament\Forms;
use Filament\Forms\Components\Builder\Block;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Z3d0X\FilamentFabricator\PageBlocks\PageBlock;

class ContactPage extends PageBlock
{
    public static function getBlockSchema(): Block
    {
        return Block::make('contact-page')
            ->schema([
                //HERO BLADE TITLE
                Forms\Components\TextInput::make('hero_description')
                        ->label('Hero title')
                        ->helperText(config('customer-website.contact-page-block.hero_helper_text')),
                //HOME PAGE HERO IMAGE IMAGE
                Forms\Components\FileUpload::make("heading_image")
                        ->required()
                        ->image()
                        ->label("Heading Image")
                        ->disk('public')
                        ->directory('site-images')
                        ->visibility('private')
                        ->acceptedFileTypes(['image/jpeg'])
                        ->getUploadedFileNameForStorageUsing(
                            fn (Forms\Get $get, TemporaryUploadedFile $file): string => (string) "contact-page-hero-image.jpg"
                        ),
                //Inquire BUTTON
                Forms\Components\TextInput::make('inquire_button')
                        ->label('Inquire button text')
                        ->helperText(config('customer-website.contact-page-block.inquire_button_helper_text')),
                //CHECK LOCATION BUTTON
                Forms\Components\TextInput::make('check_location_button')
                        ->label('Check location button text')
                        ->helperText(config('customer-website.contact-page-block.check_location_button_helper_text')),
                //FREQUENTLY ASKED QUESTION
                Forms\Components\Repeater::make('frequently_asked_questions')
                        ->helperText(config('customer-website.contact-page-block.frequently_asked_helper_text'))
                        ->schema([
                            Forms\Components\TextInput::make("question"),
                            Forms\Components\MarkdownEditor::make("answer"),
                        ])
                        ->label('Frequently Asked Question(s)'),
                 //HEAD OFFICE
                Forms\Components\TextInput::make('head_office_address')
                        ->helperText(config('customer-website.contact-page-block.head_office_text_helper_text'))
                        ->label('Head Office Address Text'),
                 //CONTACT NUMBER
                Forms\Components\TextInput::make('contact_number')
                        ->helperText(config('customer-website.contact-page-block.contact_number_helper_text'))
                        ->label('Contact Number'),
                 //EMAIL ADDRESS
                Forms\Components\TextInput::make('email_address')
                        ->helperText(config('customer-website.contact-page-block.email_address_helper_text'))
                        ->label('Email address'),
            ]);
    }

    public static function mutateData(array $data): array
    {
        return $data;
    }
}