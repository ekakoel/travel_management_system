<section class="backend-panel backend-detail-side-card hotel-detail-context-panel">
    <div class="backend-section-header">
        <div>
            <span class="backend-section-header__label">Context</span>
            <h2>Quick Actions</h2>
            <p>Operational shortcuts for maintaining hotel inventory and visual assets.</p>
        </div>
    </div>

    @canany(['posDev','posAuthor'])
        <div class="backend-detail-side-actions">
            <a href="{{ route('admin.hotels.rooms.create', $hotel->id) }}" class="backend-toolbar-action">
                <i class="fa fa-bed"></i>
                Add Room
            </a>
            <a href="{{ route('admin.hotels.gallery.edit', $hotel->id) }}" class="backend-toolbar-action">
                <i class="fa fa-picture-o"></i>
                Edit Gallery
            </a>
            <a href="{{ route('admin.hotels.promos.create', $hotel->id) }}" class="backend-toolbar-action">
                <i class="fa fa-percent"></i>
                Add Promo
            </a>
            <a href="{{ route('admin.hotels.packages.create', $hotel->id) }}" class="backend-toolbar-action">
                <i class="fa fa-cubes"></i>
                Add Package
            </a>
            <a href="{{ route('admin.hotels.additional-charges.create', $hotel->id) }}" class="backend-toolbar-action">
                <i class="fa fa-plus-circle"></i>
                Add Additional Charge
            </a>
        </div>
    @else
        <div class="backend-empty-state backend-empty-state--compact">
            <i class="fa fa-lock"></i>
            <strong>Read-only access.</strong>
            <span>You can review this hotel, but editing actions are restricted.</span>
        </div>
    @endcanany
</section>
<section class="backend-panel backend-detail-side-card hotel-detail-audit-panel">
    <div class="backend-section-header">
        <div>
            <span class="backend-section-header__label">Audit</span>
            <h2>Record Ownership</h2>
            <p>Who created the record and which currency context is used for calculations.</p>
        </div>
    </div>

    <ul class="backend-detail-side-list">
        <li>
            <span>Created</span>
            <strong>{{ dateTimeFormat($hotel->created_at) }}</strong>
            <small>Initial record timestamp.</small>
        </li>
        <li>
            <span>Author</span>
            <strong>{{ $author?->name ?: '-' }}</strong>
            <small>Record owner or creator.</small>
        </li>
    </ul>
</section>
