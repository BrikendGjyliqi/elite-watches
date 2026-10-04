<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\WatchResource;
use App\Models\Watch;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class StockAlertsTable extends BaseWidget
{
    protected static ?int $sort = 9;

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Stock · Attention required';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Watch::query()->with('brand:id,name')->lowStock()->orderBy('stock'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->extraAttributes(['class' => 'elite-serif-cell']),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand'),
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference')
                    ->placeholder('—')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('In stock')
                    ->numeric()
                    ->extraAttributes(fn (Watch $record): array => [
                        'class' => $record->stock === 0 ? 'elite-stock-zero' : 'elite-stock-low',
                    ]),
            ])
            ->recordUrl(fn (Watch $record): string => WatchResource::getUrl('edit', ['record' => $record]))
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Restock')
                    ->icon('heroicon-o-arrow-path')
                    ->button()
                    ->color('gray')
                    ->modalHeading(fn (Watch $record): string => "Restock {$record->name}")
                    ->modalDescription('Add newly received pieces to the vault.')
                    ->modalSubmitActionLabel('Add to stock')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Pieces received')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(1)
                            ->required(),
                    ])
                    ->action(function (Watch $record, array $data): void {
                        $record->increment('stock', (int) $data['quantity']);

                        Notification::make()
                            ->title('Stock replenished')
                            ->body("{$record->name} now has {$record->stock} in the vault.")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyState(view('filament.widgets.partials.stock-empty'))
            ->paginated(false);
    }
}
