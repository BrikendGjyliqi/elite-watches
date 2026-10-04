<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactInquiryResource\Pages;
use App\Mail\ContactInquiryReplyMail;
use App\Models\ContactInquiry;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Messages from the Contact page: read, reply to, archive.
 */
class ContactInquiryResource extends Resource
{
    protected static ?string $model = ContactInquiry::class;

    protected static ?string $slug = 'inquiries';

    protected static ?string $navigationGroup = 'Acquisitions';

    protected static ?string $navigationLabel = 'Inquiries';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 9;

    protected static ?string $modelLabel = 'inquiry';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $new = ContactInquiry::where('status', 'new')->count();

        return $new > 0 ? (string) $new : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Unanswered inquiries';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(fn (ContactInquiry $record): string => $record->reference())
                ->columns(2)
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable()->url(fn (ContactInquiry $record): string => 'mailto:'.$record->email),
                    TextEntry::make('phone')->placeholder('—')->copyable(),
                    TextEntry::make('preferred_channel')->label('Prefers a reply by')
                        ->formatStateUsing(fn (ContactInquiry $record): string => $record->channelLabel()),
                    TextEntry::make('subject')->formatStateUsing(fn (ContactInquiry $record): string => $record->subjectLabel()),
                    TextEntry::make('created_at')->label('Received')->dateTime('l j F Y · H:i'),
                    TextEntry::make('message')->columnSpanFull()->extraAttributes(['class' => 'whitespace-pre-line']),
                ]),
            Section::make('Reply')
                ->visible(fn (ContactInquiry $record): bool => filled($record->reply) || $record->status === 'replied')
                ->columns(2)
                ->schema([
                    TextEntry::make('repliedBy.name')->label('Replied by')->placeholder('—'),
                    TextEntry::make('replied_at')->dateTime('j M Y · H:i')->placeholder('—'),
                    TextEntry::make('reply')->placeholder('Marked as replied outside the panel (phone, WhatsApp or direct email).')
                        ->columnSpanFull()->extraAttributes(['class' => 'whitespace-pre-line']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('repliedBy:id,name'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Reference')
                    ->formatStateUsing(fn (ContactInquiry $record): string => $record->reference())
                    ->extraAttributes(['class' => 'elite-request-number'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('From')
                    ->description(fn (ContactInquiry $record): string => $record->email)
                    ->searchable(['name', 'email']),
                Tables\Columns\TextColumn::make('subject')
                    ->formatStateUsing(fn (ContactInquiry $record): string => $record->subjectLabel()),
                Tables\Columns\TextColumn::make('preferred_channel')
                    ->label('Reply by')
                    ->formatStateUsing(fn (ContactInquiry $record): string => $record->channelLabel())
                    ->icon(fn (string $state): string => match ($state) {
                        'phone' => 'heroicon-o-phone',
                        'whatsapp' => 'heroicon-o-chat-bubble-oval-left',
                        default => 'heroicon-o-envelope',
                    })
                    ->iconColor('warning')
                    ->description(fn (ContactInquiry $record): ?string => $record->preferred_channel !== 'email' ? $record->phone : null),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ContactInquiry::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'replied' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable()
                    ->tooltip(fn (ContactInquiry $record): string => $record->created_at->timezone(config('app.timezone'))->format('l j F Y · H:i'))
                    ->extraAttributes(['class' => 'elite-italic-cell']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ContactInquiry::STATUSES)
                    ->default('new'),
                Tables\Filters\SelectFilter::make('subject')
                    ->options(ContactInquiry::SUBJECTS),
            ])
            ->recordAction('view')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver()
                    ->modalWidth(MaxWidth::TwoExtraLarge),
                static::replyAction(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('markReplied')
                        ->label('Mark as replied')
                        ->icon('heroicon-o-check')
                        ->visible(fn (ContactInquiry $record): bool => $record->status === 'new')
                        ->action(fn (ContactInquiry $record) => $record->update([
                            'status' => 'replied',
                            'replied_at' => now(),
                            'replied_by' => auth()->id(),
                        ])),
                    Tables\Actions\Action::make('archive')
                        ->icon('heroicon-o-archive-box')
                        ->visible(fn (ContactInquiry $record): bool => $record->status !== 'archived')
                        ->action(fn (ContactInquiry $record) => $record->update(['status' => 'archived'])),
                    Tables\Actions\Action::make('restore')
                        ->label('Move back to inbox')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->visible(fn (ContactInquiry $record): bool => $record->status === 'archived')
                        ->action(fn (ContactInquiry $record) => $record->update(['status' => $record->replied_at ? 'replied' : 'new'])),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('archive')
                    ->icon('heroicon-o-archive-box')
                    ->deselectRecordsAfterCompletion()
                    ->action(fn (Collection $records) => $records->each->update(['status' => 'archived'])),
            ])
            ->emptyStateIcon('heroicon-o-envelope-open')
            ->emptyStateHeading('No inquiries here · the atelier is quiet.')
            ->emptyStateDescription(null);
    }

    /** Write back to the client from the panel; the reply is emailed and kept on record. */
    public static function replyAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('reply')
            ->icon('heroicon-o-paper-airplane')
            ->button()
            ->color('gray')
            ->extraAttributes(['class' => 'elite-review-btn'])
            ->visible(fn (ContactInquiry $record): bool => $record->status !== 'archived')
            ->modalHeading(fn (ContactInquiry $record): string => 'Reply to '.$record->name)
            ->modalDescription(fn (ContactInquiry $record): string => "Sent by email to {$record->email}, signed by the atelier. Their message is quoted below your reply.")
            ->modalSubmitActionLabel('Send reply')
            ->modalWidth(MaxWidth::TwoExtraLarge)
            ->form([
                Textarea::make('reply')
                    ->label('Your reply')
                    ->required()
                    ->rows(8)
                    ->maxLength(5000),
            ])
            ->action(function (ContactInquiry $record, array $data, Tables\Actions\Action $action): void {
                $record->fill([
                    'reply' => trim($data['reply']),
                    'status' => 'replied',
                    'replied_at' => now(),
                    'replied_by' => auth()->id(),
                ]);

                try {
                    Mail::to($record->email)->send(new ContactInquiryReplyMail($record));
                } catch (Throwable $exception) {
                    report($exception);
                    Notification::make()->title('The reply could not be sent')->body('Nothing was saved. Please try again in a moment.')->danger()->send();
                    $action->halt();
                }

                $record->save();

                Notification::make()->title('Reply sent')->body("{$record->name} will receive it at {$record->email}.")->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactInquiries::route('/'),
        ];
    }
}
