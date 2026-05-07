# Safe Deletion / Cleanup Report

> Status: One non-runtime task script was deleted after re-verification.
> Deleted: `tasks/import_uc29_37.php`
> Reason: The script was outside the MVC runtime, had no runtime references, and attempted to read missing `database.sql`.

## 1. Deletion Decision

Current recommendation: do not delete the detected scaffold/TODO MVC files automatically.

Most detected items are still connected to the project through at least one of these mechanisms:

- Dynamic MVC routing through `core/Router.php`
- Automatic model loading in `index.php`
- `registerAggregate(...)` references between models
- Existing views/forms that post to controller actions
- Role-based navigation or redirect paths
- Planning/developer task documents

Because of that, deleting them may break routes, diagrams, future implementation points, or runtime autoloading.

## 2. Items Requested For Cleanup Review

### Housekeeper Module

Files:

- `app/controllers/HousekeeperController.php`
- `app/models/Housekeeper.php`
- `app/views/housekeeper/*`

Issue:

- Controller actions such as `store`, `update`, and `delete` are TODO-only.
- Model methods are TODO-only.
- Views explicitly contain placeholder/scaffold data.

Deletion safety:

- Not safe to remove automatically.

Reason:

- `HousekeeperController` is reachable through the dynamic router using `/housekeeper/...`.
- `AuthController::getRedirectPath()` references the housekeeper role flow.
- `app/views/housekeeper/*` links to the same routes.
- `Housekeeper` is referenced by `HousekeepingTask` and `QAInspection` aggregates.

Recommendation:

- Implement or formally deprecate the module.
- Do not delete until the housekeeper route, role redirect, views, and aggregate references are removed or replaced together.

## 3. Supervisor Module

Files:

- `app/controllers/SupervisorController.php`
- `app/views/supervisor/*`
- Missing/expected: `app/models/Supervisor.php`

Issue:

- Controller is scaffold-only for write actions.
- Views contain placeholder data.
- `Supervisor` model file is absent in the current workspace.

Deletion safety:

- Not safe to remove automatically.

Reason:

- `SupervisorController` is reachable through `/supervisor/...`.
- `AuthController::getRedirectPath()` redirects supervisor users to `supervisor/index`.
- Housekeeper views link to supervisor routes.
- Supervisor role appears in housekeeping, maintenance, and frontdesk authorization logic.

Recommendation:

- Either implement the supervisor workspace or remove the full supervisor route/view/redirect flow in one reviewed change.

## 4. Revenue Manager Scaffold

Files:

- `app/controllers/RevenueManagerController.php`
- `app/models/RevenueManager.php`
- `app/views/revenue_manager/*`

Issue:

- `RevenueManagerController::store`, `update`, and `delete` are empty/TODO-only.
- `RevenueManager` model methods are TODO-only.
- Revenue manager workspace has partially hard-coded/dashboard-style data.

Deletion safety:

- Not safe to remove automatically.

Reason:

- `RevenueManagerController` is reachable through `/revenue_manager/...`.
- `AuthController::getRedirectPath()` routes revenue manager users there.
- Layout navigation links to revenue manager and revenue reports.

Recommendation:

- Keep and implement, or deprecate only after rerouting revenue manager users.

## 5. Revenue Manager Virtual Inventory Partial Stubs

File:

- `app/models/RevenueManagerVirtualInventory.php`

Missing methods:

- `getRoomVirtualCost`
- `getGuestVirtualConsumption`
- `getDepartmentCosts`
- `calculateRevenueImpact`
- `checkCostLimits`
- `generateFinancialSummary`
- `linkBillingFolio`

Issue:

- These methods are empty.
- Other methods in the same class are implemented, including inventory grid, sync status, virtual max adjustment, overbooking check, and change logging.

Deletion safety:

- Not safe to remove automatically.

Reason:

- The class is actively used by `RevenueManagerVirtualInventoryController`.
- Removing the whole class would break implemented inventory flows.
- Removing only empty methods may still affect UML diagrams or future planned calls.

Recommendation:

- Implement missing methods or mark them formally deprecated in documentation.

## 6. Billing Placeholder Models

Files:

- `app/models/Folio.php`
- `app/models/FolioCharge.php`
- `app/models/Payment.php`

Issue:

- `Folio.php` currently contains only attributes and constructor/aggregate registration.
- `FolioCharge.php` methods are TODO-only.
- `Payment.php` methods are TODO-only.

Deletion safety:

- Not safe to remove automatically.

Reason:

- `Folio` registers aggregates to `FolioCharge` and `Payment`.
- `Reservation`, `RevenueManager`, and billing-related flows reference folios.
- Developer task docs explicitly mention these models as intended implementation targets.

Recommendation:

- Implement the placeholder models or refactor billing diagrams/docs to show current implemented flow through `GuestBilling`, `GroupBilling`, and `PaymentService`.

## 7. Operations Placeholder Models

Files:

- `app/models/HousekeepingTask.php`
- `app/models/MaintenanceOrder.php`
- `app/models/LostAndFound.php`

Issue:

- These models contain TODO-only CRUD and workflow methods.

Deletion safety:

- Not safe to remove automatically.

Reason:

- `Room` registers aggregates to `HousekeepingTask` and `MaintenanceOrder`.
- `Housekeeper` registers aggregates to `HousekeepingTask` and `MaintenanceOrder`.
- `FoundItem` appears to implement the newer lost-and-found flow, but `LostAndFound` may still be a legacy/planned model.
- Developer task docs reference these models.

Recommendation:

- Treat as legacy/planned scaffolds.
- Do not delete unless all aggregate references, docs, and expected UML/sequence diagrams are updated.

## 8. Task Script Cleanup Executed

File:

- `tasks/import_uc29_37.php`

Issue:

- It reads `../database.sql`.
- Current root SQL file in the workspace is `hotel_management.sql`.

Deletion safety after re-check:

- Safe enough to remove from runtime perspective.

Reason:

- It is not part of routing, autoloading, controllers, models, views, helpers, or services.
- It reads `../database.sql`, but no `database.sql` exists in the project root.
- The current SQL dump file is `hotel_management.sql`.
- Re-scan found no runtime references to `tasks/import_uc29_37.php`.

Action taken:

- Deleted `tasks/import_uc29_37.php`.

Impact warning:

- Runtime MVC application should not be affected.
- If a developer expected to run this old manual script, they now need to use/update the current SQL dump workflow instead.

## 9. Safe Cleanup Suggestions

No MVC application item is currently labeled `SAFE TO REMOVE (REVIEW REQUIRED)`.

Reason:

- Every suspected MVC cleanup target has at least one reference, route path, aggregate relationship, view link, autoload side effect, or dynamic-use possibility.

Safe actions that can be done without deletion:

- Update UML diagrams to mark scaffold/TODO classes clearly.
- Add documentation notes for placeholder modules.
- Create an implementation backlog for missing methods.
- Create a separate deprecation plan before removing any controller/model/view.

## 10. If Deletion Is Still Required

Deletion should be done only after explicit review of one of these groups:

1. Full Housekeeper scaffold removal:
   - Remove controller, model, views, role redirects, nav links, aggregate references.

2. Full Supervisor scaffold removal:
   - Remove controller, views, role redirects, links from housekeeper views, authorization assumptions.

3. Legacy model removal:
   - Remove aggregate references and update diagrams before deleting model files.

4. Task script archival:
   - Completed for `tasks/import_uc29_37.php`.

## 11. Final Recommendation

Do not delete additional MVC files automatically.

Best next step:

- Decide whether each scaffold module should be implemented or formally deprecated.
- After that decision, perform a reviewed cleanup in one module at a time.
