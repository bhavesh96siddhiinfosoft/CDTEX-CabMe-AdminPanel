<div class="sidebar-search">
    <input type="text" id="sideBarSearchInput" placeholder="{{trans('lang.search_menu')}}" autocomplete="one-time-code" onkeyup="filterMenu()">
</div>
<nav class="sidebar-nav">
    <ul id="sidebarnav">
        <li>
            <a class="waves-effect waves-dark" href="{!! url('/dashboard') !!}">
                <i class="mdi mdi-home"></i>
                <span class="hide-menu">{{ trans('lang.dashboard') }}</span>
            </a>
        </li>
        
        @canany(['roles.index', 'admin-users.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.access_management') }}</span></li>
            <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-lock-outline"></i>
                    <span class="hide-menu">{{ trans('lang.access_control') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('roles.index')
                        <li><a href="{!! route('roles.index') !!}">{{ trans('lang.role_plural') }}</a></li>
                    @endcan
                    @can('admin-users.index')
                        <li><a href="{!! route('admin-users.index') !!}">{{ trans('lang.admin_plural') }}</a></li>
                    @endcan
                </ul>
            </li>
        @endcanany
        
        @can('live-tracking.index')
        <li>
            <a class="waves-effect waves-dark" href="{!! route('live-tracking.index') !!}">
                <i class="mdi mdi-home-map-marker"></i>
                <span class="hide-menu">{{ trans('lang.live_tracking') }}</span>
            </a>
        </li>
        @endcan

        @canany(['roles.index', 'rides.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.zones_rides_management') }}</span></li>
            @can('zone.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('zone.index') !!}" aria-expanded="false">
                    <i class="mdi mdi-map-marker-circle"></i>
                    <span class="hide-menu">{{ trans('lang.zones') }}</span>
                </a>
            </li>
            @endcan
            @can('surgezone.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('surgezone.index') !!}" aria-expanded="false">
                    <i class="mdi mdi-trending-up"></i>
                    <span class="hide-menu">{{ trans('lang.surge_zone_plural') }}</span>
                </a>
            </li>
            @endcan
            @can('rides.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('rides.index') !!}">
                    <i class="mdi mdi-map-marker-multiple"></i>
                    <span class="hide-menu">{{ trans('lang.all_rides') }}</span>
                </a>
            </li>
            @endcan
        @endcanany
        
        @canany(['users.index', 'dispatcher-users.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.users_management') }}</span></li>
            @can('users.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('users.index') !!}">
                    <i class="mdi mdi-account-multiple"></i>
                    <span class="hide-menu">{{ trans('lang.customers') }}</span>
                </a>
            </li>
            @endcan
            @can('dispatcher-users.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('dispatcher-users.index') !!}">
                    <i class="mdi mdi-account-box"></i>
                    <span class="hide-menu">{{ trans('lang.dispatchers') }}</span>
                </a>
            </li>
            @endcan
        @endcanany

        @can('owners.index')
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.owner_management') }}</span></li>
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-account"></i>
                    <span class="hide-menu">{{ trans('lang.owner_plural') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    <li><a href="{!! url('owners') !!}">{{ trans('lang.all_owners') }}</a></li>
                    <li><a href="{!! url('owners/approved') !!}">{{ trans('lang.approved_owners') }}</a></li>
                    <li><a href="{!! url('owners/pending') !!}">{{ trans('lang.pending_owners') }}</a></li>
                </ul>
            </li>
        @endcan
        @can('drivers.index')
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.driver_management') }}</span></li>
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-account-card-details"></i>
                    <span class="hide-menu">{{ trans('lang.driver_plural') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    <li><a href="{!! url('drivers') !!}">{{ trans('lang.all_drivers') }}</a></li>
                    <li><a href="{!! url('drivers/approved') !!}">{{ trans('lang.approved_drivers') }}</a></li>
                    <li><a href="{!! url('drivers/pending') !!}">{{ trans('lang.pending_drivers') }}</a></li>
                </ul>
            </li>
        @endcan
        @can('fleetdrivers.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! url('fleet-drivers') !!}">
                    <i class="mdi mdi-account-box"></i>
                    <span class="hide-menu">{{ trans('lang.fleet_drivers') }}</span>
                </a>
            </li>           
        @endcan
        @can('driver-document.index')
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.documents_management') }}</span></li>
            <li>
                <a class="waves-effect waves-dark" href="{!! route('driver-document.index') !!}">
                    <i class="mdi mdi-file-document-outline"></i>
                    <span class="hide-menu">{{ trans('lang.documents') }}</span>
                </a>
            </li>
        @endcan
        @can('coupons.index')
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.promotions_management') }} </span></li>
            <li>
                <a class="waves-effect waves-dark" href="{!! route('coupons.index') !!}">
                    <i class="mdi mdi-sale"></i>
                    <span class="hide-menu">{{ trans('lang.coupon_plural') }}</span>
                </a>
            </li>
        @endcan
        
        @canany(['subscription-plans.index', 'subscription-history.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.business_setup') }}</span></li>
            <li><a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-credit-card"></i>
                    <span class="hide-menu">{{ trans('lang.subscription_plans') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('subscription-plans.index')
                        <li><a href="{!! route('subscription-plans.index') !!}">{{ trans('lang.subscription_plans') }}</a></li>
                    @endcan
                    @can('subscription-history.index')
                        <li><a href="{!! route('subscription-history.index') !!}">{{ trans('lang.subscription_history_plural') }}</a></li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['parcel-category.index', 'parcel.index', 'parcel-charges.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.service_management') }}</span></li>
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-package"></i>
                    <span class="hide-menu">{{ trans('lang.parcel') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('parcel-category.index')
                        <li><a href="{!! route('parcel-category.index') !!}">{{ trans('lang.parcel_category') }}</a></li>
                    @endcan
                    @can('parcel.index')
                        <li><a href="{!! route('parcel.index') !!}">{{ trans('lang.parcel_bookings') }}</a></li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['rental-packages.index', 'rental-orders.index'])
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-car"></i>
                    <span class="hide-menu">{{ trans('lang.rental') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('rental-packages.index')
                        <li><a href="{!! route('rental-packages.index') !!}">{{ trans('lang.rental_packages') }}</a></li>
                    @endcan
                    @can('rental-orders.index')
                        <li><a href="{!! route('rental-orders.index') !!}">{{ trans('lang.rental_orders') }}</a></li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['vehicle-type.index', 'brand.index', 'car-model.index'])
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-car-connected"></i>
                    <span class="hide-menu">{{ trans('lang.vehicle_settings') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('vehicle-type.index')
                        <li><a href="{!! route('vehicle-type.index') !!}">{{ trans('lang.vehicle_type') }}</a></li>
                    @endcan
                    @can('brand.index')
                        <li><a href="{!! route('brand.index') !!}">{{ trans('lang.brand') }}</a></li>
                    @endcan
                    @can('car-model.index')
                        <li><a href="{!! route('car-model.index') !!}">{{ trans('lang.model') }}</a></li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['complaints.index', 'sos.index', 'notifications.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.customer_support') }}</span></li>
            @can('complaints.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('complaints.index') !!}">
                    <i class="fa fa-list-alt"></i>
                    <span class="hide-menu">{{ trans('lang.complaints') }}</span>
                </a>
            </li>
            @endcan
            @can('sos.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('sos.index') !!}">
                    <i class="fa fa-heartbeat"></i>
                    <span class="hide-menu">{{ trans('lang.sos') }}</span>
                </a>
            </li>
            @endcan
            @can('notifications.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('notifications.index') !!}">
                    <i class="fa fa-table"></i>
                    <span class="hide-menu">{{ trans('lang.notification_plural') }}</span>
                </a>
            </li>
            @endcan
        @endcanany

        
        @canany(['userreport.index', 'driverreport.index', 'ownerreport.index', 'travelreport.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.reports_management') }}</span></li>
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-chart-box-outline"></i>
                    <span class="hide-menu">{{ trans('lang.reports') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('userreport.index')
                    <li><a href="{!! route('userreport.index') !!}">{{ trans('lang.user_reports') }}</a></li>
                    @endcan
                    @can('driverreport.index')
                    <li><a href="{!! route('driverreport.index') !!}">{{ trans('lang.driver_reports') }}</a></li>
                    @endcan
                    @can('ownerreport.index')
                    <li><a href="{!! route('ownerreport.index') !!}">{{ trans('lang.owner_reports') }}</a></li>
                    @endcan
                    @can('travelreport.index')
                    <li><a href="{!! route('travelreport.index') !!}">{{ trans('lang.travel_reports') }}</a></li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['driversPayouts.index', 'payoutRequests'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.disbursement_management') }}</span></li>
            <li>
                <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                    <i class="mdi mdi-bank"></i>
                    <span class="hide-menu">{{ trans('lang.disbursements') }}</span>
                </a>
                <ul aria-expanded="false" class="collapse">
                    @can('driversPayouts.index')
                    <li><a href="{!! route('driversPayouts.index') !!}">{{ trans('lang.drivers_disbursements') }}</a></li>
                    @endcan
                    @can('payoutRequests')
                    <li><a href="{!! route('payoutRequests') !!}">{{ trans('lang.payout_request') }}</a></li>
                    @endcan
                   
                </ul>
            </li>
        @endcanany

        @canany(['banners.index', 'on-boarding.index', 'cms.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.content_management') }}</span></li>
            @can('banners.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('banners.index') !!}">
                    <i class="mdi mdi-monitor-multiple "></i>
                    <span class="hide-menu">{{ trans('lang.banner_items') }}</span>
                </a>
            </li>
            @endcan
            @can('cms.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('cms.index') !!}">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    <span class="hide-menu">{{ trans('lang.cms_plural') }}</span>
                </a>
            </li>
            @endcan
            @can('on-boarding.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('on-boarding.index') !!}">
                    <i class="mdi mdi-monitor-multiple"></i>
                    <span class="hide-menu">{{ trans('lang.on_boarding_screens') }}</span>
                </a>
            </li>
            @endcan
            @can('email-template.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('email-template.index') !!}">
                    <i class="mdi mdi-email-outline"></i>
                    <span class="hide-menu">{{ trans('lang.email_template_plural') }}</span>
                </a>
            </li>
            @endcan
        @endcanany

        @canany(['language.index', 'currency.index', 'tax.index'])
            <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.localisation_payments') }}</span></li>
            @can('language.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('language.index') !!}">
                    <i class="mdi mdi-translate"></i>
                    <span class="hide-menu">{{ trans('lang.administration_tools_languages') }}</span>
                </a>
            </li>
            @endcan
            @can('currency.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('currency.index') !!}">
                    <i class="mdi mdi-currency-usd"></i>
                    <span class="hide-menu">{{ trans('lang.currencies') }}</span>
                </a>
            </li>
            @endcan
            {{-- Taxes and Payment Methods sit beside Languages and Currencies, the
                 way foodie groups them. --}}
            @can('tax.index')
            <li>
                <a class="waves-effect waves-dark" href="{!! route('tax.index') !!}">
                    <i class="mdi mdi-cash-multiple"></i>
                    <span class="hide-menu">{{ trans('lang.taxes') }}</span>
                </a>
            </li>
            @endcan
            <li>
                <a class="waves-effect waves-dark" href="{!! route('payment.stripe') !!}">
                    <i class="mdi mdi-credit-card-outline"></i>
                    <span class="hide-menu">{{ trans('lang.administration_payment_methods') }}</span>
                </a>
            </li>
        @endcanany
        
        

        

        {{-- One Settings entry. Everything that used to be listed flat here is a
             card in the hub at /settings, including terms and privacy policy. --}}
        @canany(['general-settings.edit', 'commission.edit', 'driver-document.index', 'maintenance-settings.edit', 'terms-condition.edit', 'privacy-policy.edit'])
        <li class="nav-subtitle"><span class="nav-subtitle-span">{{ trans('lang.settings_configurations') }}</span></li>
        <li>
            <a class="waves-effect waves-dark" href="{!! route('settings') !!}">
                <i class="mdi mdi-cog-outline"></i>
                <span class="hide-menu">{{ trans('lang.settings') }}</span>
            </a>
        </li>
        @endcanany
    </ul>
    <p class="web_version">V: {{ $app_setting->web_version }}</p>
</nav>

<script>
    function filterMenu() {
        const searchInput = document.getElementById('sideBarSearchInput').value.toLowerCase();
        const menuItems = document.getElementById('sidebarnav').getElementsByTagName('li');
        for (let i = 0; i < menuItems.length; i++) {
            const item = menuItems[i];
            const itemText = item.textContent.toLowerCase();
            if (itemText.indexOf(searchInput) === -1) {
                item.style.display = 'none';
            } else {
                item.style.display = '';
            }
        }
    }

    // Mini-sidebar hover flyout. When the rail is collapsed the labels are hidden,
    // so hovering an item pops a fixed panel with its label and submenu links.
    // Only `top` is set here — the horizontal edge comes from CSS, which flips the
    // panel to the other side of the rail under [dir="rtl"].
    window.addEventListener('load', function () {
        var panel = document.createElement('div');
        panel.id = 'mini-hover-panel';
        document.body.appendChild(panel);
        var hideTimer;

        function showPanel(li) {
            clearTimeout(hideTimer);
            var rect = li.getBoundingClientRect();
            var labelEl = li.querySelector('a .hide-menu');
            var label = labelEl ? labelEl.textContent.trim() : '';
            // :scope pins this to `li`'s own children — without it the parent <a>
            // also matches and shows up as a duplicate first row.
            var subLinks = li.querySelectorAll(':scope > ul > li > a');
            var hasSub = subLinks.length > 0;

            var html = '<div class="mhp-label' + (hasSub ? ' has-sub' : '') + '">' + label + '</div>';
            subLinks.forEach(function (a) {
                html += '<a class="mhp-link" href="' + (a.getAttribute('href') || '#') + '">' +
                            a.textContent.trim() +
                        '</a>';
            });
            panel.innerHTML = html;

            panel.style.top = rect.top + 'px';
            panel.style.display = 'block';

            // Nudge back up if the panel would run past the bottom of the viewport.
            var pr = panel.getBoundingClientRect();
            if (pr.bottom > window.innerHeight - 10) {
                var adjusted = window.innerHeight - pr.height - 10;
                panel.style.top = (adjusted < 10 ? 10 : adjusted) + 'px';
            }
        }

        document.querySelectorAll('#sidebarnav > li').forEach(function (li) {
            // Section headings have no link of their own.
            if (li.classList.contains('nav-subtitle')) return;
            li.addEventListener('mouseenter', function () {
                if (!document.body.classList.contains('mini-sidebar')) return;
                showPanel(li);
            });
            li.addEventListener('mouseleave', function () {
                hideTimer = setTimeout(function () { panel.style.display = 'none'; }, 130);
            });
        });

        panel.addEventListener('mouseenter', function () { clearTimeout(hideTimer); });
        panel.addEventListener('mouseleave', function () {
            hideTimer = setTimeout(function () { panel.style.display = 'none'; }, 130);
        });
    });
</script>
