# Phase 4: Fleet Command Center

Phase 4 adds the FleetOS operational homepage: a dark, responsive Mission Control surface that answers what needs attention today. No Phase 1 database design or Phase 2 import behavior was redesigned.

## Architecture

- `Home::index()` remains thin. It requests a prepared view model from `FleetCommandCenterViewModelService` and renders the page.
- `FleetCommandService` remains the operational source of truth for the Command Center snapshot: fleet status, vehicle status, today's timeline, urgent work, and future signal placeholders.
- `FleetCommandCenterViewModelService` composes that snapshot with supplemental Phase 3 service outputs into display-ready arrays for the UI.
- Business calculations remain in Phase 3 services. Financial metrics come from `RevenueService` and `FleetStatisticsService`; operational tasks come from `TaskService`, `FleetHealthService`, `VehicleAvailabilityService`, and `FleetCommandService`.
- `AssetManifestService` resolves built Vite asset paths before rendering, so views do not parse manifests or perform file-system asset lookup.
- Views render prepared arrays and reusable components. They do not query the database and do not calculate fleet metrics.

## Route

- `GET /` renders the Fleet Command Center.

## View Model Sections

- `fleet_status` powers the fleet status metric cards.
- `mission` powers Today's Mission task cards.
- `vehicles` powers Fleet Activity cards for every Spaceship.
- `timeline` powers Today, Tomorrow, and Next 7 Days timeline cards.
- `financial` powers the Financial Snapshot cards.
- `health_alerts` powers warnings-only Fleet Health.
- `executive_kpis` powers owner-focused performance metrics.
- `activity` powers the right-side operational activity panel.
- `future_integrations` reserves space for Tesla API, weather, traffic, Google Maps, flight tracking, push notifications, SMS, email, and calendar sync.

## Reusable Components

Components live under `app/Views/fleet_command_center/components`:

- `navigation.php`
- `metric_card.php`
- `status_badge.php`
- `task_card.php`
- `vehicle_card.php`
- `timeline_card.php`
- `alert_card.php`
- `financial_card.php`
- `activity_panel.php`

These components are intentionally generic enough to be reused by future Fleet, Revenue, Claims, Maintenance, and Reports pages.

## Design System

The UI is dark-mode first and styled in `resources/css/app.css`.

- Desktop uses left navigation, top status bar, main content, and right activity panel.
- Tablet hides the right activity panel and keeps operational content primary.
- Mobile uses a sticky disclosure-style navigation drawer, then priority sections, task cards, activity cards, and timeline.
- Cards use 8px radii, restrained borders, dense spacing, and high-contrast text.
- Color is reserved for operational priority: success, info, warning, and danger.

## Accessibility

- A skip link targets the main content area.
- Primary and mobile navigation have ARIA labels.
- The current page uses `aria-current="page"`.
- The main content area is focusable for keyboard navigation.
- Color is paired with text labels and counts.
- Focus states are visible on links, summary controls, and focusable regions.

## Future Integrations

Future-only integrations are placeholders, not fake data. Battery, location, weather, traffic, and external signals display as reserved/future states until real integrations exist.

## Performance Notes

- The controller performs no SQL and no metric calculations.
- The UI consumes a single prepared view model.
- Built asset paths are resolved by `AssetManifestService`, outside the view layer.
- The Phase 3 services continue to own optimized reads and calculations.
- Future caching should wrap `FleetCommandCenterViewModelService::forToday()` or the underlying Phase 3 services, not the view files.

## Architectural Audit

- No business calculations were moved into controllers.
- No SQL was added to controllers or views.
- No query builder calls were added to Fleet Command Center view files.
- No manifest parsing or asset file reads are performed by Fleet Command Center views.
- Financial, utilization, ADR, RevPAD, ROI, cash flow, and forecast values are consumed from Fleet Intelligence services.
- Future metrics return `null`, empty arrays, `Future`, or `Reserved` display states rather than fabricated data.
- Reusable components are isolated as partials.
- Navigation, layout, status cards, mission tasks, vehicle activity, timeline, financial snapshot, health warnings, executive KPIs, and future integration placeholders are implemented.
- Phase 1 and Phase 2 files were not redesigned.
