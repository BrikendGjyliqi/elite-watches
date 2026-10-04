<?php

namespace App\Filament\Resources;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Acquisitions';

    protected static ?string $navigationLabel = 'All records';

    protected static ?int $navigationSort = 10;

    /** Stages an admin may set by hand once a request has been decided. */
    protected const FULFILMENT_STATUSES = ['awaiting_payment', 'paid', 'shipped', 'delivered', 'cancelled'];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    protected static function statusFormSchema(): array
    {
        return [
            Forms\Components\Select::make('status')
                // Approve / decline / request info live on the review desk, which reserves stock and emails the client.
                ->options(fn (?Order $record): array => array_intersect_key(
                    Order::STATUS_LABELS,
                    array_flip([...self::FULFILMENT_STATUSES, $record?->status]),
                ))
                ->required()
                ->helperText('To approve or decline an open request, use the Acquisitions review desk.'),
            Forms\Components\TextInput::make('note')
                ->label('Internal note (optional)')
                ->maxLength(255),
        ];
    }

    protected static function handleStatusUpdate(Order $record, array $data): void
    {
        $fromStatus = $record->status;

        if ($fromStatus === $data['status']) {
            return;
        }

        $record->update(['status' => $data['status']]);

        $record->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $data['status'],
            'changed_by' => auth()->id(),
            'note' => $data['note'] ?? null,
        ]);

        // Stubbed customer notification — actually delivered via the "log" mail driver in this environment.
        Mail::to($record->user->email)->send(new OrderStatusUpdatedMail($record));

        Notification::make()
            ->title("Order {$record->order_number} marked as {$data['status']}")
            ->success()
            ->send();
    }

    public static function updateStatusAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('updateStatus')
            ->label('Update status')
            ->icon('heroicon-o-arrow-path')
            ->color('primary')
            ->form(static::statusFormSchema())
            ->fillForm(fn (Order $record) => ['status' => $record->status])
            ->action(fn (Order $record, array $data) => static::handleStatusUpdate($record, $data));
    }

    public static function updateStatusHeaderAction(): HeaderAction
    {
        return HeaderAction::make('updateStatus')
            ->label('Update status')
            ->icon('heroicon-o-arrow-path')
            ->color('primary')
            ->form(static::statusFormSchema())
            ->fillForm(fn (Order $record) => ['status' => $record->status])
            ->action(fn (Order $record, array $data) => static::handleStatusUpdate($record, $data));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => AcquisitionResource::statusColor($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('preferred_method')
                    ->label('Settlement')
                    ->formatStateUsing(fn (?string $state): string => $state ? config("concierge.methods.{$state}.short") : '—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Placed at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Order::STATUS_LABELS),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                static::updateStatusAction(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(OrderExporter::class),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()->exporter(OrderExporter::class),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\StatusHistoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_number', 'user.name', 'user.email'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Customer' => $record->user?->name,
            'Status' => $record->statusLabel(),
        ];
    }
}
