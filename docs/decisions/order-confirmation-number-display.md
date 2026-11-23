# Order Confirmation Number Display

Status: active

Agent-facing detail pages for active public services display the service
confirmation number only when the stored value is non-empty. When no value is
available, the label and value are both omitted; a dash or empty row is not
shown.

The shared order accessor remains the source of truth. For Accommodation it
continues to fall back to the existing hotel confirmation number field. This is
a read-only presentation change: it does not generate, modify, or expose the
number through a new endpoint. Existing order ownership checks remain in force.
