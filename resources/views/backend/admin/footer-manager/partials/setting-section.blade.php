<section class="footer-setting-section">
    <button class="footer-setting-section__toggle {{ $isOpen ? '' : 'collapsed' }}" type="button" data-toggle="collapse" data-target="#footerSettingGroup{{ $groupKey }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="footerSettingGroup{{ $groupKey }}">
        <span class="footer-setting-section__icon">
            <i class="{{ $group['icon'] }}" aria-hidden="true"></i>
        </span>
        <span>
            <strong>{{ $group['title'] }}</strong>
            <small>{{ $group['description'] }}</small>
        </span>
        <span class="footer-setting-section__meta">{{ $activeCount }}/{{ $groupSettings->count() }} active</span>
    </button>

    <div id="footerSettingGroup{{ $groupKey }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-parent="#footerSettingsAccordion">
        <div class="footer-setting-section__body">
            @foreach ($groupSettings as $setting)
                <div class="footer-setting-item">
                    <div class="footer-setting-item__head">
                        <div>
                            <strong>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</strong>
                            <span>{{ $setting->key }}</span>
                        </div>
                        <label class="footer-switch">
                            <input type="hidden" name="settings[{{ $setting->id }}][status]" value="0">
                            <input type="checkbox" name="settings[{{ $setting->id }}][status]" value="1" @checked(old("settings.{$setting->id}.status", $setting->status))>
                            <span>Active</span>
                        </label>
                    </div>

                    <div class="backend-form-grid footer-setting-input-grid">
                        <label>
                            <span>English / Default</span>
                            <textarea name="settings[{{ $setting->id }}][value]" rows="2" class="backend-form-control @error("settings.{$setting->id}.value") is-invalid @enderror" data-backend-richtext="true">{{ old("settings.{$setting->id}.value", $setting->value) }}</textarea>
                            @error("settings.{$setting->id}.value")
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </label>
                        <label>
                            <span>Chinese Traditional</span>
                            <textarea name="settings[{{ $setting->id }}][value_traditional]" rows="2" class="backend-form-control @error("settings.{$setting->id}.value_traditional") is-invalid @enderror" data-backend-richtext="true">{{ old("settings.{$setting->id}.value_traditional", $setting->value_traditional) }}</textarea>
                            @error("settings.{$setting->id}.value_traditional")
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </label>
                        <label>
                            <span>Chinese Simplified</span>
                            <textarea name="settings[{{ $setting->id }}][value_simplified]" rows="2" class="backend-form-control @error("settings.{$setting->id}.value_simplified") is-invalid @enderror" data-backend-richtext="true">{{ old("settings.{$setting->id}.value_simplified", $setting->value_simplified) }}</textarea>
                            @error("settings.{$setting->id}.value_simplified")
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
