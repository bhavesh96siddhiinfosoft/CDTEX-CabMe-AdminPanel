/**
 * Shared behaviour for the ec-* form controls.
 *
 * Shared form behaviour. Foodie has no other global form
 * script, so this file carries the pieces every converted form needs and is
 * loaded once from the layout — new forms get the behaviour for free rather
 * than repeating it per page.
 */

// Styled file inputs (.ec-upload) — show the chosen file name in the label.
// Delegated so it also covers uploads rendered after page load.
$(document).on('change', '.ec-upload input[type="file"]', function () {
    var $label = $(this).closest('.ec-upload');
    var file = this.files && this.files[0];
    if (file) {
        $label.addClass('has-file').find('.ec-upload-text').text(file.name);
    } else {
        $label.removeClass('has-file').find('.ec-upload-text')
            .text($label.data('placeholder') || 'Click to upload');
    }
});

/**
 * SweetAlert2 in place of the browser's native alert().
 *
 * foodie raises ~105 native alert() calls across ~56 views. Rather than edit
 * every call site (and miss the ones added later), window.alert is routed
 * through Swal — the library is already loaded from the layout. Call sites stay
 * exactly as they are and every one of them gets the styled popup.
 *
 * Native alert() blocks; Swal does not. The call sites here all follow
 * `alert(msg); return;`, so nothing depends on the blocking behaviour.
 */
(function () {
    if (typeof toastr === 'undefined') return;   // layout didn't load it — keep the native alert
    var nativeAlert = window.alert;

    window.alert = function (message) {
        var text = (message === undefined || message === null) ? '' : String(message);
        // Messages phrased as a success read better with the success icon.
        var isSuccess = /success|updated|created|deleted|saved/i.test(text);
        if (isSuccess) { toastr.success(text); } else { toastr.error(text); }
    };
    window.ecNativeAlert = nativeAlert;           // escape hatch if ever needed

    // Shared helpers, available to any page that wants them explicitly
    window.showSuccessMessage = function (message) {
        toastr.success(message);
    };
    window.showErrorMessage = function (message) {
        toastr.error(message);
    };
})();

/**
 * Dependent settings fields.
 *
 * Some toggles gate a field that is meaningless when they are off — the story
 * duration only applies when restaurants may upload stories, and the platform
 * fee amount only when the fee is enabled. Global Settings hid these; the split
 * cards lost that behaviour, so it is restored here declaratively.
 *
 * Add data-requires="<checkbox id>" to any .form-group to have it follow that
 * checkbox, rather than wiring each pair up by hand.
 */
$(function () {
    function syncDependents() {
        $('[data-requires]').each(function () {
            var $dep = $(this);
            var src = document.getElementById($dep.data('requires'));
            if (!src) return;
            // .form-group carries display:block !important in shell.css, so an
            // inline display:none from .toggle() loses — use a class instead.
            $dep.toggleClass('ec-dep-hidden', !src.checked);
        });
    }
    $(document).on('change', 'input[type="checkbox"]', syncDependents);
    // settings load asynchronously, so re-sync once the values land
    $(document).on('ec:settings-loaded', function () { setTimeout(syncDependents, 120); });
    setTimeout(syncDependents, 1500);
});

/* ---------------------------------------------------------------------------
 * Radio-card groups (.ec-choice-group)
 *
 * Marks the .ec-choice-row whose radio is checked with .is-active, which tints
 * the card and reveals the field that choice needs. Delegated and generic, so a
 * page only needs the markup — no per-page wiring.
 *
 * Clicking anywhere on the card selects its radio, which is what the card
 * affordance implies. The radio's own change event still fires, so each page's
 * existing handler runs untouched.
 * ------------------------------------------------------------------------- */
$(function () {
    function syncChoiceCards(scope) {
        $(scope || '.ec-choice-group').each(function () {
            $(this).find('.ec-choice-row').each(function () {
                var $row = $(this);
                var input = $row.find('input[type="radio"]').get(0);
                $row.toggleClass('is-active', !!(input && input.checked));
            });
        });
    }

    $(document).on('change', '.ec-choice-group input[type="radio"]', function () {
        // a group is exclusive, so re-sync the whole group this radio belongs to
        syncChoiceCards($(this).closest('.ec-choice-group'));
    });

    $(document).on('click', '.ec-choice-row', function (e) {
        if ($(e.target).is('input, select, textarea, label, a, button')) return;
        var $radio = $(this).find('input[type="radio"]').first();
        if ($radio.length && !$radio.prop('checked')) $radio.prop('checked', true).trigger('change');
    });

    syncChoiceCards();
    // the value often arrives from Firestore after first paint
    $(document).on('ec:settings-loaded', function () { setTimeout(syncChoiceCards, 120); });
    setTimeout(syncChoiceCards, 1200);
});

/* ---------------------------------------------------------------------------
 * Styled dropdowns everywhere
 *
 * select2 and its stylesheet are loaded globally by layouts/app.blade.php, and
 * shell.css already themes it (brand-tinted highlighted option, form-card border
 * and radius). But only the list/filter pages ever called .select2() — 54 selects
 * across 31 views were left as the browser's native control, which is why form
 * dropdowns looked older than the ones on the listings.
 *
 * This initialises the rest. Deliberately left alone:
 *   - anything already initialised (a page that calls .select2() itself)
 *   - .chosen() multi-selects on foods / restaurants / bulk import
 *   - <select multiple>, and selects belonging to summernote or DataTables
 *   - anything opted out with data-no-select2
 *
 * Runs on a delay so page-level .select2() calls, which are registered after this
 * file, get there first — re-initialising an existing instance stacks two widgets.
 * ------------------------------------------------------------------------- */
$(function () {
    if (!$.fn || !$.fn.select2) return;

    function skip($s) {
        return $s.data('select2') ||
               $s.hasClass('select2-hidden-accessible') ||
               $s.is('[multiple]') ||
               $s.is('[data-no-select2]') ||
               $s.hasClass('chosen-select') ||
               $s.hasClass('chosen') ||
               // The topbar language <select> is deliberately display:none — the
               // .ec-lang pill is its UI. Auto-initialising it rendered a white
               // select2 box on top of the pill.
               $s.hasClass('changeLang') ||
               $s.hasClass('country_code') ||
               $s.hasClass('ql-cc-native') ||
               // a select personally marked d-none is intentionally dead UI —
               // select2 would render a visible widget beside the hidden select
               $s.hasClass('d-none') ||
               $s.attr('aria-hidden') === 'true' ||
               $s.closest('.topbar, .ec-lang').length > 0 ||
               $s.closest('.note-editor, .dataTables_wrapper, .select2-container, .swal2-container').length > 0 ||
               // A select with an inline onchange runs page logic when it changes —
               // the listings use it to submit their per-page form. resync() below
               // re-fires change on every select2 select twice a second, which turned
               // that into an endless reload loop. Leave these as native controls.
               $s.is('[onchange]') ||
               // a select the page itself hides inline (SweetAlert's internal <select>, dependent fields) is not UI yet
               ($s[0].style && $s[0].style.display === 'none');
    }

    function styleSelects(root) {
        $(root || document).find('select').each(function () {
            var $s = $(this);
            if (skip($s)) return;

            // the "-- Select X --" option doubles as the placeholder, the way the
            // filter cards already do it
            var $blank = $s.find('option[value=""]').first();
            // No search box inside short lists — a two-option "Search by" dropdown
            // with its own text filter looks broken. Long selects keep it.
            var opts = { width: '100%', dropdownAutoWidth: true, minimumResultsForSearch: 8 };
            if ($blank.length && $blank.text().trim()) {
                opts.placeholder = $blank.text().trim();
            }
            var $modal = $s.closest('.modal');
            if ($modal.length) opts.dropdownParent = $modal;

            try { $s.select2(opts); } catch (e) { /* a malformed select must not stop the rest */ }
        });
    }

    /* Most of these selects are filled from Firestore after first paint, and a
       programmatic .val() fires no change event, so select2 would keep showing the
       placeholder over a set value. Re-render the selection text, cheaply. */
    function resync() {
        $('select.select2-hidden-accessible').each(function () {
            var $s = $(this);
            // Never re-fire change on a select that carries page logic, even if the
            // page initialised select2 on it itself.
            if ($s.is('[onchange]')) return;
            if ($s.val() !== null && $s.val() !== '') $s.trigger('change.select2');
        });
    }

    setTimeout(function () { styleSelects(); resync(); }, 700);
    /* Values arrive from Firestore at unpredictable times and a programmatic
       .val() fires no event, so select2 would keep showing the placeholder over a
       set value. Poll briefly and repair only what is actually out of sync. */
    var settle = setInterval(function () { styleSelects(); resync(); }, 700);
    setTimeout(function () { clearInterval(settle); }, 12000);
    $(document).on('ec:settings-loaded', function () { setTimeout(function () { styleSelects(); resync(); }, 150); });

    // options arriving later (Firestore, or a dependent dropdown being refilled)
    if (window.MutationObserver) {
        var pending = null;
        new MutationObserver(function (records) {
            var relevant = records.some(function (r) {
                if (r.target && (r.target.tagName === 'SELECT' ||
                       (r.target.parentNode && r.target.parentNode.tagName === 'SELECT'))) return true;
                // rows built by page JS (working hours / special offers) arrive as
                // whole subtrees holding brand-new selects — the mutation target is
                // the row, so also look inside what was added
                return Array.prototype.some.call(r.addedNodes || [], function (n) {
                    return n.nodeType === 1 && (n.tagName === 'SELECT' ||
                           (n.querySelector && n.querySelector('select')));
                });
            });
            if (!relevant) return;
            clearTimeout(pending);
            pending = setTimeout(function () { styleSelects(); resync(); }, 250);
        }).observe(document.body, { childList: true, subtree: true });
    }
});


/* ---------------------------------------------------------------------------
 * Errors surface as a corner toast
 *
 * Pages report validation failures by writing into a `.error_top` div:
 *     $(".error_top").show(); $(".error_top").html("");
 *     $(".error_top").append("<p>Please enter zone name</p>");
 * which paints a red band above the card — 388 such calls across 82 views.
 *
 * Rather than rewrite every call site (and miss any added later), the div is
 * watched: whatever is written into it becomes a toastr toast and the div is
 * emptied and hidden. toastr is already loaded by the layout, and this is the
 * same treatment showError() itself uses.
 *
 * Several call sites append two or three <p> errors in a row, so messages are
 * buffered for a tick and raised as one toast each, deduplicated.
 * ------------------------------------------------------------------------- */
$(function () {
    if (typeof toastr === 'undefined') return;   // no toastr: leave the inline strip alone

    // one-shot flash: a page that reloads or redirects right after a successful write
    // stores its message here; the next page shows it as a success toast.
    window.ecFlash = function (msg) { try { sessionStorage.setItem('ecFlashMsg', msg); } catch (e) { } };
    try {
        var ecFlashMsg = sessionStorage.getItem('ecFlashMsg');
        if (ecFlashMsg) { sessionStorage.removeItem('ecFlashMsg'); setTimeout(function () { toastr.success(ecFlashMsg); }, 200); }
    } catch (e) { }

    toastr.options = $.extend({}, toastr.options, {
        positionClass: 'toast-top-right',
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        preventDuplicates: true,
        timeOut: 5000,
    });

    var flushTimer = null;
    var buffer = [];

    function flush() {
        var items = buffer.slice();
        buffer = [];
        items.forEach(function (msg) { toastr.error(msg); });
    }

    function drain(el) {
        var $e = $(el);
        if (!$e.length || !$.trim($e.text())) return;

        // each <p> is one message; fall back to the whole text when there are none
        var lines = $e.find('p').map(function () { return $.trim($(this).text()); }).get().filter(Boolean);
        if (!lines.length) lines = [$.trim($e.text())];
        lines = lines
            .filter(function (l) { return l.indexOf('__DEMO_BLOCKED__') === -1; })
            .map(function (l) { return l === '[object Object]' ? ((window.ecStrings && ecStrings.somethingWrong) || 'Something went wrong. Please try again.') : l; })
            .filter(Boolean);
        if (!lines.length) { $e.empty().hide(); return; }
        lines.forEach(function (l) { if (buffer.indexOf(l) === -1) buffer.push(l); });

        // emptying re-triggers the observer, which then sees no text and returns
        $e.empty().hide();

        clearTimeout(flushTimer);
        flushTimer = setTimeout(flush, 60);
    }

    function watch(el) {
        if (el.__ecErrorWatched) return;
        el.__ecErrorWatched = true;
        new MutationObserver(function () { drain(el); })
            .observe(el, { childList: true, subtree: true, characterData: true });
        drain(el);                              // anything already rendered server-side
    }

    function scan() {
        $('.error_top').each(function () { watch(this); });
        $('.success_top').each(function () {
            if (this.__ecOkWatched) return;
            this.__ecOkWatched = true;
            var el = this;
            new MutationObserver(function () {
                var $e = $(el), t = $.trim($e.text());
                if (!t) return;
                $e.empty().hide();
                toastr.success(t);
            }).observe(el, { childList: true, subtree: true });
        });
    }

    scan();
    if (window.MutationObserver) {
        new MutationObserver(scan).observe(document.body, { childList: true, subtree: true });
    }
});

/* ---------------------------------------------------------------------------
 * Phone country picker on every phone field
 *
 * Binds PhonePicker (phone-picker.js) to each `select.country_code`
 * inside a .phone-box: flag+code button joined to the number input, opening a
 * searchable flag/name/code menu. The native select stays in the DOM as the
 * source of truth, so each page's save handler and Firestore default-country
 * logic keep working unchanged.
 *
 * Several pages still run their own `$('#country_selector').select2({...})`
 * with flag templates — that widget is destroyed first, or both UIs render.
 * Delayed passes cover pages that initialise late.
 * ------------------------------------------------------------------------- */
$(function () {
    if (!window.PhonePicker) return;

    // Pages call `$('#country_selector').select2({flag templates})` in their own
    // document.ready, which runs AFTER this one — destroying the widget afterwards
    // still let both UIs flash side by side for a moment. Instead, make select2 a
    // no-op on country selects so the duplicate widget is never created. Method
    // calls ('destroy', 'val', …) still pass through untouched.
    if ($.fn.select2 && !$.fn.select2._ppWrapped) {
        var _select2 = $.fn.select2;
        $.fn.select2 = function () {
            if (typeof arguments[0] === 'string') return _select2.apply(this, arguments);
            var keep = this.filter(function () {
                return !$(this).hasClass('country_code') && !$(this).hasClass('ql-cc-native');
            });
            if (keep.length) _select2.apply(keep, arguments);
            return this;
        };
        $.fn.select2._ppWrapped = true;
        $.fn.select2.defaults = _select2.defaults;
        $.fn.select2.amd = _select2.amd;
    }

    function bindPickers() {
        $('.phone-box select.country_code, .phone-box select[id^="country_selector"]').each(function () {
            var $s = $(this);
            // Pages init their own select2 on this select AFTER the first bind ran,
            // so the cleanup has to repeat even once the picker is attached — or a
            // second widget sits between the picker and the number input.
            if ($s.data('select2')) {
                try { $s.select2('destroy'); } catch (e) { }
            }
            $s.siblings('.select2-container').remove();
            if (this._ppBound) return;
            try { window.PhonePicker.bindToSelect(this); } catch (e) { }
        });
    }

    bindPickers();
    setTimeout(bindPickers, 800);
    setTimeout(bindPickers, 2500);
    setTimeout(bindPickers, 5000);
});

/* ------------------------------------------------------------------------------------------------
 * Point map for view pages — honours the admin map setting (settings/DriverNearBy.selectedMapType):
 * "osm" renders a Leaflet map with a marker, anything else the Google embed. Usage:
 *   ecRenderPointMap('.mapouter', lat, lng)
 * The container is shown; if it holds an <iframe class="gmap_iframe"> that iframe is reused.
 * ---------------------------------------------------------------------------------------------- */
window.ecMapTypePromise = window.ecMapTypePromise || null;
window.ecMapType = function () {
    if (!window.ecMapTypePromise) {
        window.ecMapTypePromise = new Promise(function (resolve) {
            try {
                var db = window.database || firebase.firestore();
                db.collection('settings').doc('DriverNearBy').get().then(function (d) {
                    resolve(d.exists && d.data() && d.data().selectedMapType == 'osm' ? 'osm' : 'google');
                }).catch(function () { resolve('google'); });
            } catch (e) { resolve('google'); }
        });
    }
    return window.ecMapTypePromise;
};
window.ecRenderPointMap = function (container, lat, lng, zoom) {
    var $box = jQuery(container).first();
    if (!$box.length) return Promise.resolve();
    zoom = zoom || 14;
    return window.ecMapType().then(function (type) {
        $box.show();
        if (type === 'osm' && typeof L !== 'undefined') {
            var id = 'ec_osm_map_' + Math.floor(Math.random() * 1e6);
            $box.html('<div id="' + id + '" class="gmap_canvas"></div>');
            var map = L.map(id, { scrollWheelZoom: false }).setView([lat, lng], zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
            L.marker([lat, lng]).addTo(map);
            setTimeout(function () { map.invalidateSize(); }, 400);
            return map;
        }
        var $if = $box.find('iframe.gmap_iframe');
        if (!$if.length) {
            $box.html('<div class="gmap_canvas"><iframe class="gmap_iframe" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe></div>');
            $if = $box.find('iframe.gmap_iframe');
        }
        $if.attr('src', 'https://maps.google.com/maps?width=600&height=225&hl=en&q=' + lat + ',' + lng + '&t=&z=' + zoom + '&ie=UTF8&iwloc=B&output=embed');
        return null;
    });
};

/* ------------------------------------------------------------------------------------------------
 * Form helpers: e-mail format check, and Firebase/auth errors rendered as plain sentences
 * ("The email address is badly formatted." instead of "FirebaseError: Firebase: … (auth/invalid-email)").
 * ---------------------------------------------------------------------------------------------- */
window.ecIsEmail = function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(v || '').trim()); };
window.ecFriendlyError = function (err) {
    if (err == null) return '';
    var strings = window.ecStrings || {};
    // demo mode already explains itself with the SweetAlert notice — no second message
    if (err === '__DEMO_BLOCKED__' || err.message === '__DEMO_BLOCKED__') return '';
    var code = err.code || '';
    var byCode = {
        'auth/invalid-email': strings.invalidEmail || 'Please enter a valid email address.',
        'auth/email-already-in-use': strings.emailInUse || 'This email address is already in use.',
        'auth/weak-password': strings.weakPassword || 'Password should be at least 6 characters.',
        'auth/wrong-password': strings.wrongPassword || 'The password is incorrect.',
        'auth/user-not-found': strings.userNotFound || 'No account was found for this email address.',
        'auth/network-request-failed': strings.networkError || 'Network error. Please check your connection and try again.'
    };
    if (byCode[code]) return byCode[code];
    var msg = String(err.message || (err.error && err.error.message) || (err.responseJSON && err.responseJSON.message) || err);
    if (msg === '[object Object]') {
        // a plain object with no message — never show its default stringification
        return strings.somethingWrong || 'Something went wrong. Please try again.';
    }
    return msg.replace(/^FirebaseError:\s*/i, '').replace(/^Firebase:\s*/i, '').replace(/\s*\((auth|firestore|storage)\/[^)]+\)\.?\s*$/i, '.').replace(/\.\.$/, '.');
};

/* ------------------------------------------------------------------------------------------------
 * Native dialogs. A plain alert() is a notice, so it stays on the toast above; only a dialog that
 * needs an answer becomes a SweetAlert2 popup. confirm() is synchronous and cannot be replaced
 * transparently, so pages call ecConfirm(msg) -> Promise<bool> instead.
 * ---------------------------------------------------------------------------------------------- */
(function () {
    var brand = function () { return (getComputedStyle(document.documentElement).getPropertyValue('--admin-panel-color') || '#FF6839').trim(); };
    var nativeAlert = window.alert, nativeConfirm = window.confirm;
    window.ecAlert = function (msg) {
        if (!window.Swal) { nativeAlert(msg); return Promise.resolve(); }
        return Swal.fire({ text: String(msg), icon: 'info', confirmButtonColor: brand(), confirmButtonText: (window.ecStrings && ecStrings.ok) || 'OK', customClass: { popup: 'ec-swal' } });
    };
    window.ecConfirm = function (msg, opts) {
        opts = opts || {};
        if (!window.Swal) return Promise.resolve(nativeConfirm(msg));
        return Swal.fire({
            text: String(msg), icon: opts.icon || 'warning', showCancelButton: true, reverseButtons: true, focusCancel: true,
            confirmButtonText: opts.confirmText || (window.ecStrings && ecStrings.yes) || 'Yes',
            cancelButtonText: opts.cancelText || (window.ecStrings && ecStrings.cancel) || 'Cancel',
            confirmButtonColor: brand(), cancelButtonColor: '#8a90a6', customClass: { popup: 'ec-swal' }
        }).then(function (r) { return !!r.isConfirmed; });
    };
})();

/* ---------------------------------------------------------------------------
 * Bulk selection bar (listings).
 * A listing that renders #ec-bulk-bar gets it wired here: ticking any .is_open
 * row checkbox shows the bar with a live count, the header #is_active box
 * reflects all/none, and #ec-bulk-clear unticks everything. The delete action
 * inside the bar is each page's own #deleteAll anchor, so the per-page confirm
 * + URL-building scripts keep working untouched.
 * ------------------------------------------------------------------------- */
(function () {
    if (!window.jQuery) return;
    jQuery(function () {
        var $bar = jQuery('#ec-bulk-bar');
        if (!$bar.length) return;

        function refresh() {
            var $boxes = jQuery('table .is_open');
            var n = $boxes.filter(':checked').length;
            jQuery('#ec-sel-count').text(n);
            $bar.toggle(n > 0);
            jQuery('#is_active').prop('checked', $boxes.length > 0 && n === $boxes.length);
        }

        jQuery(document).on('change', 'table .is_open', refresh);
        // The page's own #is_active handler does the check-all; run after it.
        jQuery(document).on('click', '#is_active', function () { setTimeout(refresh, 0); });
        jQuery(document).on('click', '#ec-bulk-clear', function () {
            jQuery('table .is_open, #is_active').prop('checked', false);
            refresh();
        });
    });
})();

/* ---------------------------------------------------------------------------
 * Live search on the listing filter forms.
 * The Search button is gone: typing submits the filter form after a pause
 * (server-side pagination, so a round trip is the filter). Enter still submits
 * immediately via the form's native behaviour. After the reload the field
 * regains focus with the caret at the end, so typing can just continue.
 * ------------------------------------------------------------------------- */
(function () {
    if (!window.jQuery) return;
    var timer;
    jQuery(document).on('input', 'form#filterForm #search, form#perPageForm #search', function () {
        var form = this.form;
        clearTimeout(timer);
        timer = setTimeout(function () { form.submit(); }, 700);
    });
    jQuery(function () {
        var s = document.querySelector('form#filterForm #search, form#perPageForm #search');
        if (s && s.value) {
            var v = s.value;
            s.focus();
            s.value = '';
            s.value = v;   // re-setting moves the caret to the end
        }
    });
})();

/* ---------------------------------------------------------------------------
 * Page-size select on the listings.
 * With no inline onchange it is select2-styled like every other dropdown; this
 * delegated handler restores the submit-on-change. Loop-safe: resync() above
 * only fires the namespaced change.select2, which never reaches this.
 * ------------------------------------------------------------------------- */
(function () {
    if (!window.jQuery) return;
    jQuery(document).on('change', 'select#per_page', function () {
        if (this.form) this.form.submit();
    });
})();

// In-page tab strips (.menu-tab) on detail screens. Bootstrap 4's tab plugin
// expects .nav markup and throws on the plain ul/li/a strip, so the switching
// is done here; the URL hash tabs (?tab=) stay server-driven for pagination.
$(document).on('click', '.menu-tab a[href^="#"]', function (e) {
    e.preventDefault();
    var $a = $(this), target = $a.attr('href');
    $a.closest('ul').find('li').removeClass('active');
    $a.parent('li').addClass('active');
    $(target).closest('.tab-content').children('.tab-pane').removeClass('active');
    $(target).addClass('active');
});

/* ---------------------------------------------------------------------------
 * Country selects
 *
 * The option text is "India (+91)" and carries data-code="in". On its own
 * select2 renders that as flat text, so the dropdown was a wall of names with
 * the dial code buried in brackets. These templates split the two apart: flag
 * and country name on the left, dial code on the right, which is how foodie's
 * country picker reads.
 *
 * The flags are the same local SVG set foodie uses (public/scss/icons/...),
 * so nothing is fetched from a CDN.
 * ------------------------------------------------------------------------- */
(function () {
    if (!window.jQuery) return;

    var FLAGS = (window.ecFlagBase || '/scss/icons/flag-icon-css/flags');

    // "India (+91)" -> { name: "India", dial: "+91" }
    function splitOption(text) {
        // blade indents the option text across lines, so flatten it first —
        // otherwise the leading newline stops the pattern matching at all
        var clean = String(text == null ? '' : text).replace(/\s+/g, ' ').trim();
        var m = /^(.*?)\s*\((\+?[0-9]+)\)$/.exec(clean);
        return m ? { name: m[1], dial: m[2].charAt(0) === '+' ? m[2] : '+' + m[2] }
                 : { name: clean, dial: '' };
    }

    function flagFor(option) {
        var code = option && option.getAttribute ? (option.getAttribute('data-code') || '') : '';
        if (!code) return null;
        return $('<img class="img-flag" alt="">').attr('src', FLAGS + '/' + code.toLowerCase() + '.svg');
    }

    // a dropdown row: flag + name on the left, dial code pushed to the right
    window.ecCountryResult = function (state) {
        if (!state.id || !state.element) return state.text;
        var parts = splitOption(state.text);
        var $row = $('<span class="ec-country-row"></span>');
        var $flag = flagFor(state.element);
        if ($flag) $row.append($flag);
        $row.append($('<span class="ec-country-name"></span>').text(parts.name));
        if (parts.dial) $row.append($('<span class="ec-country-dial"></span>').text(parts.dial));
        return $row;
    };

    // the closed control shows just the flag and the dial code
    window.ecCountrySelection = function (state) {
        if (!state.id || !state.element) return state.text;
        var parts = splitOption(state.text);
        var $sel = $('<span class="ec-country-sel"></span>');
        var $flag = flagFor(state.element);
        if ($flag) $sel.append($flag);
        $sel.append($('<span></span>').text(parts.dial || parts.name));
        return $sel;
    };

    // select2 has no search-placeholder option for single selects, so it is set
    // on the field it creates each time the dropdown opens
    $(document).on("select2:open", function (e) {
        var $src = $(e.target);
        if (!$src.is("#country_code, #tax_country, .defaultCountryCode, .country_code, select.country")) return;
        var text = (window.ecStrings && ecStrings.searchCountry) || "Search country...";
        setTimeout(function () { $(".select2-container--open .select2-search__field").attr("placeholder", text); }, 0);
    });

    // options every country select shares
    window.ecCountrySelect2 = function (extra) {
        return $.extend({
            templateResult: window.ecCountryResult,
            templateSelection: window.ecCountrySelection,
            width: '100%',
            dropdownAutoWidth: true
        }, extra || {});
    };
})();
