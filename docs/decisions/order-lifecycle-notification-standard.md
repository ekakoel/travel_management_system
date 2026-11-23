# Order Lifecycle Notification Standard

Status: active

## Scope

Account notifications cover the shared public order services: Hotel, Hotel
Promo, Hotel Package, Transport, Tour Package, and Activity. Wedding and
Private Villa are outside this notification flow.

Notifications are sent to the account IDs recorded in `orders.user_id` and
`orders.sales_agent`, deduplicated when both IDs are the same. An absent account
does not cause delivery to an unverified address from the order form.

## Events

| Event | Trigger |
| --- | --- |
| Order submitted | Order status changes from Draft to Pending |
| Order status | Pending, Approved, Canceled, Rejected, or Invalid transition |
| Payment received | A Pending payment confirmation is created |
| Payment verified | Confirmation becomes Valid while order is not Paid |
| Payment needs attention | Confirmation becomes Invalid |
| Payment complete | Order transitions to Paid |
| Service complete | `completed_at` transitions from null to a value |

Draft saves, unrelated edits, and the transition of a DOKU payment confirmation
to Valid for an order that is already Paid do not create duplicate updates.
`Paid` remains the successful commercial order status; fulfillment is reported
only from `completed_at` and never written as an order status.

## Delivery and security

Each event is stored in the existing Laravel database notifications table and
sent by email. Delivery runs after the surrounding database transaction commits;
delivery failures are logged and do not undo an order or payment transition.
The account notification page scopes reads and mark-as-read actions to the
authenticated account's own notifications.

Messages contain an order reference, service label, and lifecycle update. They
must not include payment evidence, internal notes, invoice links, or sensitive
payment data. The email action links to the authenticated order list.

No schema or lifecycle changes are part of this decision. New event types or
services require an explicit update to this document and the lifecycle mapping.
