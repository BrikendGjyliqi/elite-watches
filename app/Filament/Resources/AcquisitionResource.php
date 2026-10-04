<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcquisitionResource\Pages;
use App\Filament\Resources\AcquisitionResource\Pages\ListAcquisitions;
use App\Models\Order;
use App\Services\AcquisitionService;
use DomainException;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

/**
 * The atelier's review desk: acquisition requests grouped into work queues,
 * each reviewed in a full-height "dossier" slide-over.
 */
class AcquisitionResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $slug = 'acquisitions';

    protected static ?string $modelLabel = 'acquisition request';

    protected static ?string $pluralModelLabel = 'acquisition requests';

    protected static ?string $navigationGroup = 'Acquisitions';

    protected static bool $isGloballySearchable = false;

    /** @var array<string, array{label: string, nav: string, icon: string, statuses: array<int, string>, empty: string}> */
    public const QUEUES = [
        'pending' => [
            'label' => 'Pending requests',
            'nav' => 'Pending requests',
            'icon' => 'heroicon-o-inbox-arrow-down',
            'statuses' => ['requested'],
            'empty' => 'No pending acquisitions · the vault is quiet.',
        ],
        'review' => [
            'label' => 'Under review',
            'nav' => 'Under review',
            'icon' => 'heroicon-o-chat-bubble-left-right',
            'statuses' => ['under_review'],
            'empty' => 'No conversations in progress.',
        ],
        'approved' => [
            'label' => 'Approved · awaiting payment',
            'nav' => 'Approved · awaiting payment',
            'icon' => 'heroicon-o-check-badge',
            'statuses' => ['approved', 'awaiting_payment'],
            'empty' => 'No approvals awaiting settlement.',
        ],
        'fulfilled' => [
            'label' => 'Fulfilled',
            'nav' => 'Fulfilled',
            'icon' => 'heroicon-o-gift',
            'statuses' => ['paid', 'shipped', 'delivered'],
            'empty' => 'No fulfilled acquisitions yet.',
        ],
        'archive' => [
            'label' => 'Declined · cancelled',
            'nav' => 'Declined · cancelled archive',
            'icon' => 'heroicon-o-archive-box',
            'statuses' => ['declined', 'cancelled'],
            'empty' => 'The archive is empty.',
        ],
    ];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function queueFor(?string $status): string
    {
        foreach (self::QUEUES as $key => $queue) {
            if (in_array($status, $queue['statuses'], true)) {
                return $key;
            }
        }

        return 'pending';
    }

    /** Deep link that opens a request's dossier directly (notifications, emails, dashboard). */
    public static function reviewUrl(Order $order): string
    {
        return static::getUrl('index', [
            'queue' => static::queueFor($order->status),
            'review' => $order->getKey(),
        ], panel: 'admin');
    }

    /** One sidebar entry per queue instead of the default single item. */
    public static function getNavigationItems(): array
    {
        $counts = Order::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $sort = 0;

        return collect(self::QUEUES)->map(function (array $queue, string $key) use ($counts, &$sort): NavigationItem {
            $count = (int) collect($queue['statuses'])->sum(fn (string $status) => $counts[$status] ?? 0);
            $showsBadge = in_array($key, ['pending', 'review', 'approved'], true) && $count > 0;

            return NavigationItem::make($queue['nav'])
                ->group(static::getNavigationGroup())
                ->icon($queue['icon'])
                ->url(static::getUrl('index', ['queue' => $key]))
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName().'.*')
                    && request()->query('queue', 'pending') === $key)
                ->badge($showsBadge ? (string) $count : null, color: $key === 'pending' ? 'warning' : 'gray')
                ->badgeTooltip($key === 'pending' && $count > 0 ? 'Awaiting your review' : null)
                ->sort($sort++);
        })->values()->all();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'items.watch.images', 'reviewingAdmin:id,name']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query, Pages\ListAcquisitions $livewire) => $query
                ->whereIn('status', self::QUEUES[$livewire->queue]['statuses'] ?? self::QUEUES['pending']['statuses']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Request')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'elite-request-number'])
                    ->description(fn (Order $record): ?string => $record->isBeingReviewedByAnotherAdmin(auth()->id())
                        ? 'Being reviewed by '.str($record->reviewingAdmin?->name)->before(' ')
                        : null),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable(['name', 'email'])
                    ->description(fn (Order $record): ?string => $record->user?->email)
                    ->url(fn (Order $record): ?string => $record->user ? UserResource::getUrl('edit', ['record' => $record->user]) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\ViewColumn::make('items')
                    ->label('Pieces')
                    ->view('filament.acquisitions.columns.items'),
                Tables\Columns\TextColumn::make('total')
                    ->money('EUR')
                    ->sortable()
                    ->extraAttributes(['class' => 'elite-money-cell']),
                Tables\Columns\TextColumn::make('preferred_method')
                    ->label('Settlement')
                    ->formatStateUsing(fn (?string $state): string => $state ? config("concierge.methods.{$state}.short") : '—')
                    ->icon(fn (?string $state): ?string => $state ? config("concierge.methods.{$state}.icon") : null)
                    ->iconColor('warning'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => static::statusColor($state))
                    ->visible(fn (Pages\ListAcquisitions $livewire): bool => count(self::QUEUES[$livewire->queue]['statuses'] ?? []) > 1),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable()
                    ->tooltip(fn (Order $record): string => $record->created_at->timezone(config('app.timezone'))->format('l j F Y · H:i'))
                    ->extraAttributes(['class' => 'elite-italic-cell']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('preferred_method')
                    ->label('Settlement')
                    ->options(collect(config('concierge.methods'))->map(fn (array $method) => $method['short'])->all()),
            ])
            ->actions([
                static::reviewAction(),
            ])
            ->recordAction('review')
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading(fn (Pages\ListAcquisitions $livewire): string => self::QUEUES[$livewire->queue]['empty'] ?? self::QUEUES['pending']['empty'])
            ->emptyStateDescription(null);
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'requested', 'under_review' => 'warning',
            'approved', 'awaiting_payment' => 'primary',
            'paid', 'shipped', 'delivered' => 'success',
            'declined' => 'danger',
            default => 'gray',
        };
    }

    /** The dossier: everything about the request on the left, the decision on the right. */
    public static function reviewAction(): Tables\Actions\Action
    {
        return Tables\Actions\Action::make('review')
            ->label(fn (Order $record): string => $record->isOpen() ? 'Review' : 'Open dossier')
            ->icon(fn (Order $record): string => $record->isOpen() ? 'heroicon-o-eye' : 'heroicon-o-folder-open')
            ->button()
            ->color('gray')
            ->extraAttributes(['class' => 'elite-review-btn'])
            ->slideOver()
            ->modalWidth(MaxWidth::SevenExtraLarge)
            ->modalHeading(fn (Order $record): string => "Acquisition #{$record->order_number}")
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close dossier')
            ->extraModalWindowAttributes(['class' => 'elite-dossier-modal'])
            ->mountUsing(function (Order $record, ?Form $form): void {
                app(AcquisitionService::class)->markReviewing($record, auth()->user());

                $form?->fill([
                    'internal_note' => $record->notes,
                    'price_adjustment' => null,
                    'personal_message' => null,
                ]);
            })
            ->form(fn (Order $record): array => [
                Grid::make(['default' => 1, 'lg' => 5])
                    ->schema([
                        View::make('filament.acquisitions.dossier')
                            ->columnSpan(['default' => 1, 'lg' => 3]),
                        Group::make(static::decisionSchema($record))
                            ->columnSpan(['default' => 1, 'lg' => 2])
                            ->extraAttributes(['class' => 'elite-dossier-aside']),
                    ]),
            ]);
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    protected static function decisionSchema(Order $record): array
    {
        $eyebrow = fn (string $text): Placeholder => Placeholder::make('eyebrow_'.md5($text))
            ->hiddenLabel()
            ->content(new HtmlString('<p class="elite-eyebrow">'.e($text).'</p>'));

        if (! $record->isOpen()) {
            return [
                $eyebrow('Outcome'),
                View::make('filament.acquisitions.outcome'),
                $eyebrow('Activity on this request'),
                View::make('filament.acquisitions.activity'),
            ];
        }

        $caption = fn (string $key, string $text): Placeholder => Placeholder::make('caption_'.$key)
            ->hiddenLabel()
            ->content(new HtmlString('<p class="elite-decision-caption">'.e($text).'</p>'));

        return [
            $eyebrow('Review decision'),

            Placeholder::make('reviewing_notice')
                ->hiddenLabel()
                ->visible(fn (): bool => $record->isBeingReviewedByAnotherAdmin(auth()->id()))
                ->content(fn (): HtmlString => new HtmlString(
                    '<p class="elite-reviewing-notice">Being reviewed by '.e($record->reviewingAdmin?->name)
                    .' since '.e($record->reviewing_started_at?->diffForHumans()).'. Coordinate before deciding.</p>'
                )),

            Actions::make([static::approveAction()])->key('decision-approve')->fullWidth(),
            $caption('approve', 'The piece is reserved and the client will be contacted.'),

            Actions::make([static::requestInfoAction()])->key('decision-info')->fullWidth(),
            $caption('info', 'Ask the client a question. The request moves to Under review.'),

            Actions::make([static::declineAction()])->key('decision-decline')->fullWidth(),
            $caption('decline', 'Closes the request. A reason is required and shared with the client.'),

            Group::make([
                Textarea::make('internal_note')
                    ->label('Internal note · admins only')
                    ->rows(3)
                    ->maxLength(2000)
                    ->placeholder('Context for colleagues — never shown to the client.'),
                TextInput::make('price_adjustment')
                    ->label('Suggested price adjustment (optional)')
                    ->prefix('€')
                    ->numeric()
                    ->minValue(1)
                    ->placeholder(number_format((float) $record->total, 2, '.', ''))
                    ->helperText('If set, replaces the request total upon approval.'),
                Textarea::make('personal_message')
                    ->label('Personal message to the client')
                    ->rows(3)
                    ->maxLength(2000)
                    ->placeholder('Included in the approval email.'),
            ])->extraAttributes(['class' => 'elite-dossier-fields']),

            $eyebrow('Activity on this request'),
            View::make('filament.acquisitions.activity'),
        ];
    }

    protected static function approveAction(): FormAction
    {
        return FormAction::make('approve')
            ->label('Approve request')
            ->icon('heroicon-o-check')
            ->color('warning')
            ->extraAttributes(['class' => 'elite-decision elite-decision--approve'])
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-check-badge')
            ->modalHeading('Approve this acquisition?')
            ->modalDescription(function (Get $get, Order $record): string {
                $total = static::adjustedTotal($get('price_adjustment')) ?? (float) $record->total;

                return 'The pieces are reserved from stock and '.$record->user?->name
                    .' receives settlement instructions for €'.number_format($total, 2).'.';
            })
            ->modalSubmitActionLabel('Approve & notify client')
            ->action(function (Get $get, Order $record, FormAction $action): void {
                $raw = $get('price_adjustment');
                $adjusted = static::adjustedTotal($raw);

                if (filled($raw) && $adjusted === null) {
                    Notification::make()->title('The price adjustment must be a positive amount.')->danger()->send();
                    $action->halt();
                }

                try {
                    $shortages = app(AcquisitionService::class)->approve(
                        $record,
                        auth()->user(),
                        adjustedTotal: $adjusted,
                        message: $get('personal_message'),
                        internalNote: $get('internal_note'),
                    );
                } catch (DomainException $exception) {
                    Notification::make()->title($exception->getMessage())->danger()->send();
                    $action->halt();
                }

                Notification::make()
                    ->title('Request approved · Client notified')
                    ->body($shortages ? 'Re-order required for: '.implode(', ', $shortages).'.' : null)
                    ->success()
                    ->send();
            })
            // Close the dossier once the decision is made (the request has left this queue).
            ->after(fn (ListAcquisitions $livewire) => $livewire->unmountTableAction());
    }

    protected static function requestInfoAction(): FormAction
    {
        return FormAction::make('requestInfo')
            ->label('Request more info')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('gray')
            ->extraAttributes(['class' => 'elite-decision elite-decision--info'])
            ->modalHeading('A question for the client')
            ->modalDescription('Your message is emailed to the client and appears on their request page, where they can reply.')
            ->modalSubmitActionLabel('Send message')
            ->modalWidth(MaxWidth::Large)
            ->form([
                Textarea::make('question')
                    ->label('Message')
                    ->required()
                    ->rows(5)
                    ->maxLength(2000)
                    ->placeholder('e.g. Could you confirm your wrist size so we can adjust the bracelet before delivery?'),
            ])
            ->action(function (array $data, Get $get, Order $record, FormAction $action): void {
                try {
                    app(AcquisitionService::class)->requestInfo($record, auth()->user(), $data['question'], $get('internal_note'));
                } catch (DomainException $exception) {
                    Notification::make()->title($exception->getMessage())->danger()->send();
                    $action->halt();
                }

                Notification::make()->title('Message sent')->body('The request is now under review.')->success()->send();
            })
            // Close the dossier once the decision is made (the request has left this queue).
            ->after(fn (ListAcquisitions $livewire) => $livewire->unmountTableAction());
    }

    protected static function declineAction(): FormAction
    {
        return FormAction::make('decline')
            ->label('Decline request')
            ->icon('heroicon-o-x-mark')
            ->color('danger')
            ->extraAttributes(['class' => 'elite-decision elite-decision--decline'])
            ->modalHeading('Decline this request')
            ->modalDescription('The client receives a graceful email with your reason and three alternative pieces.')
            ->modalSubmitActionLabel('Decline & notify client')
            ->modalWidth(MaxWidth::Large)
            ->form([
                Textarea::make('reason')
                    ->label('Reason, as the client will read it')
                    ->required()
                    ->minLength(10)
                    ->rows(5)
                    ->maxLength(2000)
                    ->placeholder('e.g. This reference has just been allocated to a long-standing client of the maison.'),
            ])
            ->action(function (array $data, Get $get, Order $record, FormAction $action): void {
                try {
                    app(AcquisitionService::class)->decline($record, auth()->user(), $data['reason'], $get('internal_note'));
                } catch (DomainException $exception) {
                    Notification::make()->title($exception->getMessage())->danger()->send();
                    $action->halt();
                }

                Notification::make()->title('Request declined · Client notified')->success()->send();
            })
            // Close the dossier once the decision is made (the request has left this queue).
            ->after(fn (ListAcquisitions $livewire) => $livewire->unmountTableAction());
    }

    protected static function adjustedTotal(mixed $value): ?float
    {
        if (blank($value) || ! is_numeric($value) || (float) $value <= 0) {
            return null;
        }

        return round((float) $value, 2);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcquisitions::route('/'),
        ];
    }
}
