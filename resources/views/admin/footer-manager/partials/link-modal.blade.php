@php
    $link = $link ?? null;
    $fieldValue = fn (string $field, $fallback = '') => old($field, $link->{$field} ?? $fallback);
    $footerGroups = [
        'services' => 'Services',
        'quick_links' => 'Quick Links',
        'policies' => 'Policies',
    ];
    $currentGroup = $fieldValue('group', 'quick_links');
    $openNewTabId = $modalId . 'OpenNewTab';
    $statusId = $modalId . 'Status';
@endphp

<div class="modal fade footer-link-modal" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="{{ $modalId }}Form" action="{{ $action }}" method="post">
                @csrf
                @if ($method !== 'post')
                    @method($method)
                @endif

                <div class="footer-link-modal__header">
                    <div>
                        <span class="footer-manager-eyebrow">Footer Navigation</span>
                        <h4 id="{{ $modalId }}Label">{{ $title }}</h4>
                        <p>Keep labels short and use route names for internal pages whenever possible.</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="footer-link-modal__body">
                    <section class="footer-link-modal__section">
                        <div class="footer-link-modal__section-title">
                            <strong>Basic Link Detail</strong>
                            <span>Shown directly inside the selected footer column.</span>
                        </div>
                        <div class="footer-link-modal__grid">
                            <div class="form-group">
                                <label>Group <span class="text-danger">*</span></label>
                                <select name="group" class="form-control" required>
                                    @foreach ($footerGroups as $groupValue => $groupLabel)
                                        <option value="{{ $groupValue }}" {{ $currentGroup === $groupValue ? 'selected' : '' }}>{{ $groupLabel }}</option>
                                    @endforeach
                                    @if (! array_key_exists($currentGroup, $footerGroups) && filled($currentGroup))
                                        <option value="{{ $currentGroup }}" selected>{{ ucwords(str_replace('_', ' ', $currentGroup)) }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Label <span class="text-danger">*</span></label>
                                <input name="label" type="text" class="form-control" value="{{ $fieldValue('label') }}" placeholder="FAQs" required>
                            </div>
                            <div class="form-group">
                                <label>Sort Order</label>
                                <input name="sort_order" type="number" min="0" class="form-control" value="{{ $fieldValue('sort_order', 0) }}" placeholder="30">
                            </div>
                        </div>
                    </section>

                    <section class="footer-link-modal__section">
                        <div class="footer-link-modal__section-title">
                            <strong>Destination</strong>
                            <span>Use only one target type. Route name is preferred for internal frontend pages.</span>
                        </div>
                        <div class="footer-link-modal__grid footer-link-modal__grid--two">
                            <div class="form-group">
                                <label>Route Name</label>
                                <input name="route_name" type="text" class="form-control" value="{{ $fieldValue('route_name') }}" placeholder="faq">
                                <small class="form-text text-muted">Example: <code>about-us</code>, <code>services</code>, <code>faq</code>.</small>
                            </div>
                            <div class="form-group">
                                <label>External URL</label>
                                <input name="url" type="text" class="form-control" value="{{ $fieldValue('url') }}" placeholder="https://example.com">
                                <small class="form-text text-muted">Only use this when the destination is outside this website.</small>
                            </div>
                        </div>
                    </section>

                    <section class="footer-link-modal__section">
                        <div class="footer-link-modal__section-title">
                            <strong>Translation & Display</strong>
                            <span>Optional labels and publishing behavior.</span>
                        </div>
                        <div class="footer-link-modal__grid footer-link-modal__grid--two">
                            <div class="form-group">
                                <label>Chinese Traditional Label</label>
                                <input name="label_traditional" type="text" class="form-control" value="{{ $fieldValue('label_traditional') }}">
                            </div>
                            <div class="form-group">
                                <label>Chinese Simplified Label</label>
                                <input name="label_simplified" type="text" class="form-control" value="{{ $fieldValue('label_simplified') }}">
                            </div>
                            <div class="form-group">
                                <label>Icon Class</label>
                                <input name="icon" type="text" class="form-control" value="{{ $fieldValue('icon') }}" placeholder="optional">
                            </div>
                            <div class="footer-link-modal__checks">
                                <input type="hidden" name="open_new_tab" value="0">
                                <label class="footer-toggle" for="{{ $openNewTabId }}">
                                    <input id="{{ $openNewTabId }}" type="checkbox" name="open_new_tab" value="1" {{ $fieldValue('open_new_tab') ? 'checked' : '' }}>
                                    <span class="footer-toggle__track" aria-hidden="true">
                                        <span class="footer-toggle__knob"></span>
                                    </span>
                                    <span class="footer-toggle__text">Open in new tab</span>
                                </label>
                                <input type="hidden" name="status" value="0">
                                <label class="footer-toggle" for="{{ $statusId }}">
                                    <input id="{{ $statusId }}" type="checkbox" name="status" value="1" {{ $link ? ($link->status ? 'checked' : '') : 'checked' }}>
                                    <span class="footer-toggle__track" aria-hidden="true">
                                        <span class="footer-toggle__knob"></span>
                                    </span>
                                    <span class="footer-toggle__text">Active</span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="footer-link-modal__footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-copy fa fa-check" aria-hidden="true"></i> Save Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
