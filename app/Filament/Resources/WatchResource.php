<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WatchResource\Pages;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Watch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class WatchResource extends Resource
{
    protected static ?string $model = Watch::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Watch')
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
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
                                Forms\Components\Select::make('brand_id')
                                    ->label('Brand')
                                    ->options(Brand::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(Category::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\TextInput::make('reference_number')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('€'),
                                Forms\Components\TextInput::make('discount_price')
                                    ->numeric()
                                    ->prefix('€')
                                    ->lt('price'),
                                Forms\Components\TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('short_description')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->rows(5)
                                    ->columnSpanFull(),
                                Forms\Components\Toggle::make('is_featured'),
                                Forms\Components\Toggle::make('is_new')
                                    ->label('New arrival'),
                                Forms\Components\Toggle::make('is_bestseller'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Images')
                            ->schema([
                                Forms\Components\Repeater::make('images')
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
                                            ->label('Image')
                                            ->image()
                                            ->directory('watch-images')
                                            ->imageEditor()
                                            // Seeded images live in public/images, not on the upload disk; without this
                                            // Filament drops them on hydrate and the edit form fails "required".
                                            ->fetchFileInformation(false)
                                            ->getUploadedFileUsing(function (Forms\Components\BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                                                if (Str::startsWith($file, ['http://', 'https://', '/'])) {
                                                    return [
                                                        'name' => basename(parse_url($file, PHP_URL_PATH) ?? $file),
                                                        'size' => 0,
                                                        'type' => null,
                                                        'url' => Str::startsWith($file, '/') ? asset($file) : $file,
                                                    ];
                                                }

                                                $storage = $component->getDisk();

                                                if (! $storage->exists($file)) {
                                                    return null;
                                                }

                                                return [
                                                    'name' => (is_array($storedFileNames) ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                                                    'size' => $storage->size($file),
                                                    'type' => $storage->mimeType($file),
                                                    'url' => $storage->url($file),
                                                ];
                                            })
                                            ->required(),
                                        Forms\Components\Toggle::make('is_primary')
                                            ->label('Primary image'),
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Add image')
                                    ->collapsible()
                                    ->defaultItems(1),
                            ]),

                        Forms\Components\Tabs\Tab::make('Specifications')
                            ->schema([
                                Forms\Components\Group::make()
                                    ->relationship('spec')
                                    ->schema([
                                        Forms\Components\TextInput::make('movement')->maxLength(255),
                                        Forms\Components\TextInput::make('case_material')->maxLength(255),
                                        Forms\Components\TextInput::make('case_diameter')->maxLength(255),
                                        Forms\Components\TextInput::make('case_thickness')->maxLength(255),
                                        Forms\Components\TextInput::make('dial_color')->maxLength(255),
                                        Forms\Components\TextInput::make('crystal')->maxLength(255),
                                        Forms\Components\TextInput::make('water_resistance')->maxLength(255),
                                        Forms\Components\TextInput::make('power_reserve')->maxLength(255),
                                        Forms\Components\TextInput::make('bracelet_material')->maxLength(255),
                                        Forms\Components\TextInput::make('weight')->maxLength(255),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->rows(3),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.path')
                    ->label('')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->state(function (Watch $record) {
                        $path = optional($record->images->firstWhere('is_primary', true) ?? $record->images->first())->path;

                        return $path && Str::startsWith($path, '/') ? asset($path) : $path;
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_price')
                    ->money('EUR')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state) => $state < 3 ? 'danger' : ($state < 10 ? 'warning' : 'success'))
                    ->weight(fn (int $state) => $state < 3 ? 'bold' : null),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_new')->boolean()->label('New'),
                Tables\Columns\IconColumn::make('is_bestseller')->boolean()->label('Bestseller'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->options(Brand::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\TernaryFilter::make('is_new'),
                Tables\Filters\TernaryFilter::make('is_bestseller'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low stock (< 3)')
                    ->query(fn (Builder $query) => $query->lowStock()),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWatches::route('/'),
            'create' => Pages\CreateWatch::route('/create'),
            'edit' => Pages\EditWatch::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'reference_number', 'brand.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Brand' => $record->brand?->name,
            'Category' => $record->category?->name,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['brand', 'category', 'images']);
    }
}
