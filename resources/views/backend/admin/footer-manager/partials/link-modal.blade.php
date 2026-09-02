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
            <form id="{{ $modalId }}Form" action="{{ $action }}" method="post" data-footer-modal-form>
                @csrf
                @if ($method !== 'post')
                    @method($method)
                @endif

                <div class="footer-link-modal__header">
                    <div>
                        <span class="footer-manager-eyebrow">Footer Navigation</span>
                        <h3 id="{{ $modalId }}Label">{{ $title }}</h3>
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
                        <div class="backend-form-grid footer-link-modal__grid">
                            <label>
                                <span>Group <b>*</b></span>
                                <select name="group" class="backend-form-control @error('group') is-invalid @enderror" required>
                                    @foreach ($footerGroups as $groupValue => $groupLabel)
                                        <option value="{{ $groupValue }}" @selected($currentGroup === $groupValue)>{{ $groupLabel }}</option>
                                    @endforeach
                                    @if (! array_key_exists($currentGroup, $footerGroups) && filled($currentGroup))
                                        <option value="{{ $currentGroup }}" selected>{{ ucwords(str_replace('_', ' ', $currentGroup)) }}</option>
                                    @endif
                                </select>
                                @error('group')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Label <b>*</b></span>
                                <input name="label" type="text" value="{{ $fieldValue('label') }}" placeholder="FAQs" class="backend-form-control @error('label') is-invalid @enderror" required>
                                @error('label')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Sort Order</span>
                                <input name="sort_order" type="number" min="0" value="{{ $fieldValue('sort_order', 0) }}" placeholder="30" class="backend-form-control @error('sort_order') is-invalid @enderror">
                                @error('sort_order')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                        </div>
                    </section>

                    <section class="footer-link-modal__section">
                        <div class="footer-link-modal__section-title">
                            <strong>Destination</strong>
                            <span>Use only one target type. Route name is preferred for internal frontend pages.</span>
                        </div>
                        <div class="backend-form-grid footer-link-modal__grid footer-link-modal__grid--two">
                            <label>
                                <span>Route Name</span>
                                <input name="route_name" type="text" value="{{ $fieldValue('route_name') }}" placeholder="faq" class="backend-form-control @error('route_name') is-invalid @enderror">
                                <small>Example: <code>about-us</code>, <code>services</code>, <code>faq</code>.</small>
                                @error('route_name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>External URL</span>
                                <input name="url" type="url" value="{{ $fieldValue('url') }}" placeholder="https://example.com" class="backend-form-control @error('url') is-invalid @enderror">
                                <small>Only use this when the destination is outside this website.</small>
                                @error('url')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                        </div>
                    </section>

                    <section class="footer-link-modal__section">
                        <div class="footer-link-modal__section-title">
                            <strong>Translation & Display</strong>
                            <span>Optional labels and publishing behavior.</span>
                        </div>
                        <div class="backend-form-grid footer-link-modal__grid footer-link-modal__grid--two">
                            <label>
                                <span>Chinese Traditional Label</span>
                                <input name="label_traditional" type="text" value="{{ $fieldValue('label_traditional') }}" class="backend-form-control @error('label_traditional') is-invalid @enderror">
                                @error('label_traditional')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Chinese Simplified Label</span>
                                <input name="label_simplified" type="text" value="{{ $fieldValue('label_simplified') }}" class="backend-form-control @error('label_simplified') is-invalid @enderror">
                                @error('label_simplified')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <label>
                                <span>Icon Class</span>
                                <input name="icon" type="text" value="{{ $fieldValue('icon') }}" placeholder="optional" class="backend-form-control @error('icon') is-invalid @enderror">
                                @error('icon')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </label>
                            <div class="footer-link-modal__checks">
                                <input type="hidden" name="open_new_tab" value="0">
                                <label class="footer-toggle" for="{{ $openNewTabId }}">
                                    <input id="{{ $openNewTabId }}" type="checkbox" name="open_new_tab" value="1" @checked($fieldValue('open_new_tab'))>
                                    <span class="footer-toggle__track" aria-hidden="true">
                                        <span class="footer-toggle__knob"></span>
                                    </span>
                                    <span class="footer-toggle__text">Open in new tab</span>
                                </label>
                                <input type="hidden" name="status" value="0">
                                <label class="footer-toggle" for="{{ $statusId }}">
                                    <input id="{{ $statusId }}" type="checkbox" name="status" value="1" @checked($link ? $fieldValue('status') : true)>
                                    <span class="footer-toggle__track" aria-hidden="true">
                                        <span class="footer-toggle__knob"></span>
                                    </span>
                                    <span class="footer-toggle__text">Active</span>
                                </label>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="backend-form-actions footer-link-modal__footer">
                    <button type="submit" class="backend-button backend-button-primary" data-footer-submit>
                        <i class="fa fa-check"></i>
                        Save Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
