<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

/**
 * Read-only audit trail backed by spatie/laravel-activitylog.
 */
class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationGroup = 'Insights';

    protected static ?string $navigationLabel = 'Audit log';

    protected static ?string $modelLabel = 'audit entry';

    protected static ?string $pluralModelLabel = 'audit log';

    protected static ?string $slug = 'audit-log';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    /** One-line, human description used by the table and the dashboard feed. */
    public static function describe(Activity $activity): string
    {
        $who = $activity->causer?->name ?? 'System';
        $what = str(class_basename((string) $activity->subject_type))->snake(' ')->toString();
        $subject = $activity->subject;
        $label = $subject?->name ?? $subject?->order_number ?? $subject?->title ?? ('#' . $activity->subject_id);

        return "{$who} {$activity->event} {$what} “{$label}”";
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['causer', 'subject']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('j M Y · H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Who')
                    ->placeholder('System')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'deleted' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Record')
                    ->formatStateUsing(fn (Activity $record): string => class_basename((string) $record->subject_type) . ' #' . $record->subject_id),
                Tables\Columns\TextColumn::make('changes')
                    ->label('Fields changed')
                    ->state(fn (Activity $record): string => collect($record->properties->get('attributes', []))->keys()->join(', '))
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options(['created' => 'Created', 'updated' => 'Updated', 'deleted' => 'Deleted']),
                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Record type')
                    ->options(fn (): array => Activity::query()->distinct()->pluck('subject_type')->filter()
                        ->mapWithKeys(fn (string $type): array => [$type => class_basename($type)])->all()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
