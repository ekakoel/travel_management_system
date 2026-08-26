document.addEventListener('DOMContentLoaded', () => {
  const previewUrls = new WeakMap();

  const parsePricingNumber = (value) => {
    const normalized = String(value || '')
      .replace(/[^\d,.-]/g, '')
      .replace(/\.(?=\d{3}(\D|$))/g, '')
      .replace(',', '.');
    const parsed = Number.parseFloat(normalized);

    return Number.isFinite(parsed) ? parsed : 0;
  };

  const formatUsd = (value) => `$${value.toLocaleString('id-ID', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;

  const formatIdr = (value) => `Rp${Math.round(value).toLocaleString('id-ID')}`;

  const initializePricingPreview = () => {
    const preview = document.querySelector('[data-activity-pricing-preview]');

    if (!preview) {
      return;
    }

    const contractRateInput = document.querySelector('#contract_rate');
    const markupInput = document.querySelector('#markup');
    const usdTarget = preview.querySelector('[data-activity-pricing-preview-usd]');
    const idrTarget = preview.querySelector('[data-activity-pricing-preview-idr]');
    const messageTarget = preview.querySelector('[data-activity-pricing-preview-message]');
    const rate = parsePricingNumber(preview.dataset.activityPricingPreviewRate);
    const tax = parsePricingNumber(preview.dataset.activityPricingPreviewTax);
    const unavailableMessage = preview.dataset.activityPricingPreviewUnavailable || 'Price cannot be calculated.';

    if (!contractRateInput || !markupInput || !usdTarget || !idrTarget || !messageTarget) {
      return;
    }

    const updatePreview = () => {
      const contractRateIdr = parsePricingNumber(contractRateInput.value);
      const markupUsd = parsePricingNumber(markupInput.value);

      if (contractRateIdr <= 0 || rate <= 0 || !Number.isFinite(tax)) {
        usdTarget.textContent = '-';
        idrTarget.textContent = '-';
        messageTarget.textContent = unavailableMessage;

        return;
      }

      const subtotalUsd = (contractRateIdr / rate) + markupUsd;
      const sellingPriceUsd = Math.ceil(subtotalUsd + (subtotalUsd * tax / 100));

      usdTarget.textContent = formatUsd(sellingPriceUsd);
      idrTarget.textContent = formatIdr(sellingPriceUsd * rate);
      messageTarget.textContent = 'Live preview only. Final price is recalculated by the server when saved.';
    };

    contractRateInput.addEventListener('input', updatePreview);
    markupInput.addEventListener('input', updatePreview);
    contractRateInput.addEventListener('change', updatePreview);
    markupInput.addEventListener('change', updatePreview);
    updatePreview();
  };

  const revokePreviewUrls = (input) => {
    const urls = previewUrls.get(input) || [];

    urls.forEach((url) => URL.revokeObjectURL(url));
    previewUrls.delete(input);
  };

  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-activity-gallery-delete]');

    if (!button) {
      return;
    }

    const activityName = button.dataset.activityGalleryDelete || 'this activity';
    const confirmed = window.confirm(`Delete this gallery image from ${activityName}?`);

    if (!confirmed) {
      event.preventDefault();
    }
  });

  document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || !input.matches('[data-activity-file-input]')) {
      return;
    }

    const target = document.querySelector(input.dataset.activityFileInputTarget || '');

    if (!target) {
      return;
    }

    const files = Array.from(input.files || []);
    const isCoverInput = input.hasAttribute('data-activity-cover-input');
    const preview = isCoverInput
      ? document.querySelector(input.dataset.activityCoverPreviewTarget || '[data-activity-cover-preview]')
      : null;
    const imageFile = files[0] || null;
    revokePreviewUrls(input);

    target.textContent = files.length > 0
      ? `${files.length} file${files.length === 1 ? '' : 's'} selected`
      : target.dataset.activityFileInputDefault || 'No file selected';

    const galleryPreview = input.dataset.activityGalleryPreviewTarget
      ? document.querySelector(input.dataset.activityGalleryPreviewTarget)
      : null;

    if (galleryPreview) {
      galleryPreview.replaceChildren();

      const imageFiles = files.filter((file) => file.type && file.type.startsWith('image/'));
      const nextPreviewUrls = [];

      if (imageFiles.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'activity-gallery-preview__empty';
        empty.innerHTML = '<i class="fa fa-images"></i><span>No selected images to preview.</span>';
        galleryPreview.appendChild(empty);
      } else {
        imageFiles.forEach((file) => {
          const previewUrl = URL.createObjectURL(file);
          nextPreviewUrls.push(previewUrl);

          const item = document.createElement('div');
          item.className = 'activity-gallery-preview__item';

          const image = document.createElement('img');
          image.src = previewUrl;
          image.alt = file.name;
          item.appendChild(image);
          galleryPreview.appendChild(item);
        });
      }

      previewUrls.set(input, nextPreviewUrls);
    }

    if (!isCoverInput || !preview || !imageFile) {
      return;
    }

    if (!imageFile.type || !imageFile.type.startsWith('image/')) {
      target.textContent = 'Selected file is not a valid image';

      return;
    }

    const nextPreviewUrl = URL.createObjectURL(imageFile);
    previewUrls.set(input, [nextPreviewUrl]);

    const image = document.createElement('img');
    image.src = nextPreviewUrl;
    image.alt = imageFile.name;
    preview.replaceChildren(image);
  });

  initializePricingPreview();
});
