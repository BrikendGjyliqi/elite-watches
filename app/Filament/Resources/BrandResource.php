<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    protected static bool $isGloballySearchable = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\FileUpload::make('logo_path')
                    ->label('Logo')
                    ->image()
                    ->directory('brand-logos')
                    // Bundled logos live in public/images/brands, not on the upload disk; without
                    // these two lines Filament drops them on load and saving blanks logo_path.
                    ->fetchFileInformation(false)
                    ->getUploadedFileUsing(fn (Forms\Components\BaseFileUpload $component, string $file): ?array => match (true) {
                        Str::startsWith($file, ['images/', '/', 'http://', 'https://']) => [
                            'name' => basename($file),
                            'size' => is_file(public_path(ltrim($file, '/'))) ? filesize(public_path(ltrim($file, '/'))) : 0,
                            'type' => Str::endsWith($file, '.svg') ? 'image/svg+xml' : null,
                            'url' => Str::startsWith($file, 'http') ? $file : asset(ltrim($file, '/')),
                        ],
                        $component->getDisk()->exists($file) => [
                            'name' => basename($file),
                            'size' => $component->getDisk()->size($file),
                            'type' => $component->getDisk()->mimeType($file),
                            'url' => $component->getDisk()->url($file),
                        ],
                        default => null,
                    }),
                Forms\Components\TextInput::make('country')
                    ->maxLength(255),
                Forms\Components\TextInput::make('founded_year')
                    ->numeric()
                    ->minValue(1700)
                    ->maxValue((int) date('Y')),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_featured'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('')
                    // Resolves both bundled logos in public/images/brands and admin uploads.
                    ->getStateUsing(fn (Brand $record): ?string => $record->logoUrl())
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('founded_year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('watches_count')
                    ->label('Watches')
                    ->counts('watches'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
