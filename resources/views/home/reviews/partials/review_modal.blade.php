<!-- resources/views/partials/review_modal.blade.php -->
<div class="modal fade" id="modalReview{{ $review->id }}" tabindex="-1" role="dialog" aria-labelledby="modalReviewLabel{{ $review->id }}" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content review-modal-content" id="printArea-{{ $review->id }}">
        <div class="card-box">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReviewLabel{{ $review->id }}">Review Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Customer:</strong> {{ $review->customer_name ?? 'Anonymous' }}</p>
                <p><strong>Date:</strong> {{ $review->created_at->format('d M Y') }}</p>
                <p><strong>Review:</strong><br>{!! nl2br(e($review->customer_review)) !!}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary print-modal-btn" data-id="{{ $review->id }}">🖨️ Print</button>
            </div>
        </div>
    </div>
  </div>
</div>
