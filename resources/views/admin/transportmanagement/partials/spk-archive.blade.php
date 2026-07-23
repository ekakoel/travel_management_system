<div class="backend-table-wrap transport-management-table-wrap transport-management-archive-table">
    <table id="spkArchived" class="backend-table transport-management-table">
        <thead>
            <tr>
                <th>@lang('transport-management.table.no')</th>
                <th>@lang('transport-management.table.date')</th>
                <th>@lang('transport-management.table.order')</th>
                <th>@lang('transport-management.table.assignment')</th>
                <th class="text-right">@lang('transport-management.table.actions')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($spk_archives as $spk)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td data-order="{{ $spk->spk_date }}">
                        <strong>{{ $spk->spk_date ? dateFormat($spk->spk_date) : '-' }}</strong>
                    </td>
                    <td>
                        <strong>{{ $spk->order_number ?? '-' }}</strong>
                        <span>{{ $spk->type ?? '-' }} / {{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</span>
                    </td>
                    <td>
                        <strong>{{ $spk->spk_number ?? '-' }}</strong>
                        <span>
                            {{ $spk->transport ? trim($spk->transport->brand . ' ' . $spk->transport->name) : __('transport-management.empty.na') }}
                            /
                            {{ $spk->driver?->name ?? __('transport-management.empty.na') }}
                        </span>
                        <small>{{ $spk->plate_number ?: '-' }}</small>
                    </td>
                    <td class="text-right">
                        <div class="backend-table-actions">
                            <button class="backend-table-action backend-table-action-view transport-management-row-action" type="button" data-toggle="modal" data-target="#spkArchiveDetail{{ $spk->id }}">
                                <i class="icon-copy dw dw-eye" aria-hidden="true"></i>
                                <span>@lang('transport-management.actions.detail')</span>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="backend-table-empty transport-management-empty">
                            <i class="icon-copy fa fa-archive" aria-hidden="true"></i>
                            <strong>@lang('transport-management.empty.archive_title')</strong>
                            <span>@lang('transport-management.empty.archive_message')</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="backend-table-card-list transport-management-card-list transport-management-archive-cards" aria-label="{{ __('transport-management.archive.mobile_label') }}">
    @forelse($spk_archives as $spk)
        <article class="backend-table-card transport-management-card">
            <div class="backend-table-card__header transport-management-card__header">
                <div>
                    <span>{{ $spk->spk_date ? dateFormat($spk->spk_date) : '-' }}</span>
                    <strong>{{ $spk->order_number ?? '-' }}</strong>
                </div>
                <button class="backend-icon-action" type="button" data-toggle="modal" data-target="#spkArchiveDetail{{ $spk->id }}" aria-label="{{ __('transport-management.actions.detail') }}">
                    <i class="icon-copy dw dw-eye" aria-hidden="true"></i>
                </button>
            </div>
            <dl class="backend-table-card-grid">
                <div>
                    <dt>@lang('transport-management.table.service')</dt>
                    <dd>{{ $spk->type ?? '-' }} / {{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</dd>
                </div>
                <div>
                    <dt>@lang('transport-management.table.assignment')</dt>
                    <dd>{{ $spk->transport ? trim($spk->transport->brand . ' ' . $spk->transport->name) : __('transport-management.empty.na') }} / {{ $spk->driver?->name ?? __('transport-management.empty.na') }}</dd>
                </div>
                <div>
                    <dt>@lang('transport-management.table.spk_number')</dt>
                    <dd>{{ $spk->spk_number ?? '-' }}</dd>
                </div>
            </dl>
        </article>
    @empty
        <div class="backend-table-empty transport-management-empty">
            <i class="icon-copy fa fa-archive" aria-hidden="true"></i>
            <strong>@lang('transport-management.empty.archive_title')</strong>
            <span>@lang('transport-management.empty.archive_message')</span>
        </div>
    @endforelse
</div>

<div id="modalContainer">
    @foreach($spk_archives as $spk)
        <div class="modal fade backend-modal transport-management-modal" id="spkArchiveDetail{{ $spk->id }}" tabindex="-1" aria-labelledby="spkArchiveDetailLabel{{ $spk->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="backend-modal__header transport-management-modal__header">
                        <div>
                            <span>@lang('transport-management.modal.eyebrow')</span>
                            <h3 id="spkArchiveDetailLabel{{ $spk->id }}">{{ $spk->order_number ?? '-' }}</h3>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('messages.Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="backend-modal__body transport-management-modal__body">
                        <dl class="transport-management-detail-grid">
                            <div>
                                <dt>@lang('transport-management.table.date')</dt>
                                <dd>{{ $spk->spk_date ? dateFormat($spk->spk_date) : '-' }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.table.service')</dt>
                                <dd>{{ $spk->type ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.modal.reserved_by')</dt>
                                <dd>{{ $spk->operator?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.form.guests')</dt>
                                <dd>{{ trans_choice('transport-management.table.pax', (int) $spk->number_of_guests, ['count' => (int) $spk->number_of_guests]) }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.form.vehicle')</dt>
                                <dd>{{ $spk->transport ? trim($spk->transport->brand . ' ' . $spk->transport->name) : '-' }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.form.driver')</dt>
                                <dd>{{ $spk->driver?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.table.spk_number')</dt>
                                <dd>{{ $spk->spk_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt>@lang('transport-management.form.plate_number')</dt>
                                <dd>{{ $spk->plate_number ?: '-' }}</dd>
                            </div>
                        </dl>

                        @if ($spk->type === 'Airport Shuttle')
                            <section class="transport-management-modal-section">
                                <h4>@lang('transport-management.modal.airport_shuttle')</h4>
                                <ol class="transport-management-timeline">
                                    @forelse($spk->airport_shuttles as $airport_shuttle)
                                        <li>
                                            <strong>{{ $airport_shuttle->date ? dateTimeFormat($airport_shuttle->date) : '-' }}</strong>
                                            <span>{{ $airport_shuttle->flight_number ?? '-' }} / {{ ($airport_shuttle->type ?? $airport_shuttle->nav) === 'In' ? __('transport-management.modal.arrival') : __('transport-management.modal.departure') }}</span>
                                        </li>
                                    @empty
                                        <li>@lang('transport-management.empty.airport_shuttle')</li>
                                    @endforelse
                                </ol>
                            </section>
                        @endif

                        <section class="transport-management-modal-section">
                            <h4>@lang('transport-management.modal.guests')</h4>
                            <ol class="transport-management-timeline">
                                @forelse($spk->guests as $guest)
                                    <li>
                                        <strong>{{ $guest->name }}</strong>
                                        <span>{{ $guest->name_mandarin ?: '-' }}</span>
                                    </li>
                                @empty
                                    <li>@lang('transport-management.empty.guests')</li>
                                @endforelse
                            </ol>
                        </section>

                        <section class="transport-management-modal-section">
                            <h4>@lang('transport-management.modal.destinations')</h4>
                            <ol class="transport-management-timeline">
                                @forelse($spk->destinations as $destination)
                                    <li>
                                        <strong>{{ $destination->destination_name ?? '-' }}</strong>
                                        <span>
                                            {{ $destination->date ? dateTimeFormat($destination->date) : '-' }}
                                            /
                                            @if ($destination->status === 'Visited')
                                                {{ __('transport-management.modal.visited_on', ['date' => $destination->visited_at ? dateTimeFormat($destination->visited_at) : '-']) }}
                                            @else
                                                @lang('transport-management.modal.expired')
                                            @endif
                                        </span>
                                    </li>
                                @empty
                                    <li>@lang('transport-management.empty.destinations')</li>
                                @endforelse
                            </ol>
                        </section>
                    </div>
                    <div class="backend-modal__footer transport-management-modal__footer">
                        <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">
                            <i class="icon-copy dw dw-cancel" aria-hidden="true"></i>
                            <span>@lang('messages.Close')</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
