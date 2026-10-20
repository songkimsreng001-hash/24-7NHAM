@extends('layout.frontend')


@section('content')
<div class="container py-4 py-lg-5">
    <!-- TOP HEADER -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="rounded-3 bg-success-subtle text-success p-2 fs-4">
            <i class="bi bi-bag-check-fill"></i>
        </div>
        <div>
            <div class="small text-success fw-bold text-uppercase">Express Delivery Checkout</div>
            <h1 class="h3 fw-bold mb-0 text-dark">Confirm Location & Payment</h1>
        </div>
    </div>

    <!-- INCOMPLETE PROFILE PROMPT (MISSING PHONE) -->
    @if($phoneMissing)
        <div class="alert alert-warning border-warning-subtle shadow-sm rounded-4 p-4 mb-4" id="phoneRequiredAlert">
            <div class="d-flex align-items-start gap-3">
                <i class="bi bi-telephone-exclamation-fill text-warning fs-3 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-1">Phone Number Required</h5>
                    <p class="small text-muted mb-3">
                        Before placing an order, please enter your contact phone number so our delivery courier can reach you upon arrival.
                    </p>
                    <form id="quickPhoneForm" class="row g-2 align-items-center" style="max-width: 520px;">
                        @csrf
                        <div class="col-8">
                            <input type="tel" id="quickPhoneInput" class="form-control rounded-3 font-monospace" placeholder="012xxxxxx (9d) or 088/096 (10d)" maxlength="10" inputmode="numeric" oninput="handlePhoneInput(this, document.getElementById('quickPhoneCounter'))" required>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-warning w-100 fw-semibold rounded-3 text-dark" onclick="saveQuickPhone()">
                                Save Phone
                            </button>
                        </div>
                    </form>
                    <div class="form-text small text-muted d-flex justify-content-between align-items-center mt-1">
                        <span><i class="bi bi-info-circle me-1"></i>088 & 096 = 10 digits; all other numbers = 9 digits</span>
                        <span id="quickPhoneCounter" class="fw-semibold">0/9 digits</span>
                    </div>
                    <div id="phoneAlertMsg" class="small mt-2 d-none"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4 justify-content-center">
        <!-- LEFT COLUMN: ADDRESS & PAYMENT -->
        <div class="col-lg-7">
            <!-- 1. LOCATION SELECTION SECTION -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="h6 fw-bold mb-0 text-dark">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>1. Delivery Location <span class="text-danger">*</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addressModal">
                        <i class="bi bi-pin-map me-1"></i> Pin on Map
                    </button>
                </div>
                <div class="card-body p-4">
                    @if($addresses->isEmpty())
                        <div class="text-center py-4 bg-light rounded-4 border border-dashed p-3">
                            <i class="bi bi-map fs-1 text-muted"></i>
                            <h6 class="fw-bold text-dark mt-2 mb-1">No Delivery Address Selected</h6>
                            <p class="small text-muted mb-3">You must confirm a delivery address before proceeding to payment.</p>
                            <button type="button" class="btn btn-success rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#addressModal">
                                <i class="bi bi-plus-lg me-1"></i> Add Address with Map Pin-Drop
                            </button>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3 mb-3" id="addressListContainer">
                            @foreach($addresses as $addr)
                                @php
                                    $dist = $addr->getDistanceFromStore();
                                    $eta  = $addr->getEtaMinutes();
                                @endphp
                                <label class="card border rounded-3 p-3 address-select-card cursor-pointer {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'border-success bg-success-subtle bg-opacity-10' : '' }}" 
                                       for="addr_{{ $addr->id }}">
                                    <div class="d-flex align-items-start gap-3">
                                        <input class="form-check-input mt-1 address-radio" 
                                               type="radio" 
                                               name="selected_address_id" 
                                               id="addr_{{ $addr->id }}" 
                                               value="{{ $addr->id }}" 
                                               data-address="{{ $addr->address_line }}" 
                                               data-phone="{{ $addr->phone }}"
                                               data-lat="{{ $addr->latitude }}"
                                               data-lng="{{ $addr->longitude }}"
                                               data-dist="{{ $dist }}"
                                               data-eta="{{ $eta }}"
                                               {{ $defaultAddress && $defaultAddress->id == $addr->id ? 'checked' : '' }}
                                               onchange="selectAddressCard(this)">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="badge bg-secondary-subtle text-dark border fw-bold">{{ $addr->label }}</span>
                                                <span class="badge bg-success text-white small">
                                                    <i class="bi bi-clock me-1"></i>~{{ $eta }} mins ({{ $dist }} km)
                                                </span>
                                            </div>
                                            <div class="fw-semibold text-dark">{{ $addr->recipient_name }} · <span class="text-muted small">{{ $addr->phone }}</span></div>
                                            <div class="text-muted small mt-1">{{ $addr->address_line }}</div>
                                            @if($addr->notes)
                                                <div class="text-muted small fst-italic mt-1"><i class="bi bi-info-circle me-1"></i>Note: {{ $addr->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-link text-success text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#addressModal">
                                <i class="bi bi-plus-circle me-1"></i> Add Another Address
                            </button>
                        </div>
                    @endif

                    <div class="mt-3">
                        <label for="delivery_notes" class="form-label small fw-semibold text-dark">Special Instructions for Courier (Optional)</label>
                        <input type="text" id="delivery_notes" class="form-control rounded-3" placeholder="e.g. Please ring doorbell, leave at lobby reception">
                    </div>
                </div>
            </div>

            <!-- 2. PAYMENT METHOD SELECTION -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="h6 fw-bold mb-0 text-dark">
                        <i class="bi bi-credit-card-2-front-fill text-success me-2"></i>2. Payment Method <span class="text-danger">*</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <!-- Card Option -->
                        <div class="col-6">
                            <label class="card border rounded-3 p-3 payment-method-card text-center cursor-pointer border-success bg-success-subtle bg-opacity-10 h-100" id="cardMethodLabel">
                                <input type="radio" name="payment_method_choice" value="card" class="d-none" checked onchange="togglePaymentMethod('card')">
                                <i class="bi bi-credit-card fs-3 text-success d-block mb-1"></i>
                                <span class="fw-bold d-block text-dark small">Card (Stripe)</span>
                                <span class="text-muted" style="font-size: .72rem;">Instant online payment</span>
                            </label>
                        </div>
                        <!-- Cash on Delivery Option -->
                        <div class="col-6">
                            <label class="card border rounded-3 p-3 payment-method-card text-center cursor-pointer h-100" id="codMethodLabel">
                                <input type="radio" name="payment_method_choice" value="cod" class="d-none" onchange="togglePaymentMethod('cod')">
                                <i class="bi bi-cash-stack fs-3 text-secondary d-block mb-1"></i>
                                <span class="fw-bold d-block text-dark small">Cash on Delivery</span>
                                <span class="text-muted" style="font-size: .72rem;">Pay when order arrives</span>
                            </label>
                        </div>
                    </div>

                    <div id="payment-error" class="alert alert-danger d-none"></div>

                    <!-- CARD FORM CONTAINER -->
                    <div id="cardPaymentSection">
                        <label class="form-label small fw-semibold text-dark">Card Details</label>
                        <div id="payment-element" class="border rounded-3 p-3 bg-light"></div>
                    </div>

                    <!-- COD INFO CONTAINER -->
                    <div id="codPaymentSection" class="d-none">
                        <div class="alert alert-info border-info-subtle rounded-3 small mb-0">
                            <i class="bi bi-info-circle-fill me-1"></i> You can pay cash directly to the delivery courier when your order arrives at your door.
                        </div>
                    </div>

                    <!-- ACTION BUTTON -->
                    <button id="pay-button" class="btn btn-success w-100 rounded-3 py-3 mt-4 fw-bold shadow-sm" type="button" onclick="submitOrder()">
                        <span id="button-text"><i class="bi bi-lock-fill me-1"></i> Place Order (<span id="btnAmountText">${{ number_format($totalAmount, 2) }}</span>)</span>
                        <span id="button-spinner" class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
                    </button>
                    
                    <div class="text-center small text-secondary mt-3">
                        <i class="bi bi-shield-check text-success me-1"></i> Safe & verified transaction with 24/7 NHAM buyer guarantee.
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: ORDER SUMMARY & PROMO VOUCHER -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 90px;">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3 d-flex justify-content-between align-items-center">
                        <span>Order Summary</span>
                        <span class="badge bg-success-subtle text-success">{{ count($cartItems) }} items</span>
                    </h2>

                    <!-- ITEMS LIST -->
                    <div class="d-flex flex-column gap-2 mb-3 overflow-auto" style="max-height: 220px;">
                        @foreach($cartItems as $item)
                            <div class="d-flex justify-content-between gap-2 small py-2 border-bottom">
                                <div class="text-truncate">
                                    <span class="fw-medium text-dark">{{ $item['name'] }}</span>
                                    <span class="text-muted">× {{ $item['quantity'] }}</span>
                                </div>
                                <strong class="text-dark">${{ number_format($item['price'] * $item['quantity'], 2) }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <!-- PROMO CODE INPUT -->
                    <div class="mb-3 pt-2">
                        <label class="form-label small fw-semibold text-dark">Have a Promo Code?</label>
                        <div class="input-group">
                            <input type="text" id="promoCodeInput" class="form-control text-uppercase font-monospace small" placeholder="e.g. WELCOME10">
                            <button class="btn btn-outline-success fw-semibold small" type="button" onclick="applyPromoCode()">Apply</button>
                        </div>
                        <div id="promoMsg" class="small mt-1 d-none"></div>
                    </div>

                    <!-- PRICE BREAKDOWN -->
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotalText">${{ number_format($totalAmount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2 d-none" id="discountRow">
                        <span class="text-success">Discount (<span id="discountBadge"></span>):</span>
                        <strong class="text-success" id="discountAmountText">-$0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Delivery Fee:</span>
                        <span class="text-success fw-semibold">Free / Included</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-3" id="etaRow">
                        <span>Estimated Arrival:</span>
                        <span class="fw-semibold text-dark" id="etaDisplay">
                            {{ $defaultAddress ? '~' . $defaultAddress->getEtaMinutes() . ' mins' : '30-45 mins' }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <strong class="fs-6">Total Due:</strong>
                        <strong class="text-success fs-5" id="finalTotalText">${{ number_format($totalAmount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LEAFLET MAP PIN-DROP & ADDRESS MODAL -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="addressModalLabel">
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>Pin-Drop Delivery Location
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-2">Drag the marker on the map to set your exact drop-off point, or search an address or landmark below. We calculate real-time distance and ETA automatically.</p>

                <!-- MAP LOCATION SEARCH BAR -->
                <div class="mb-3 position-relative">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-success">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="mapSearchInput" class="form-control border-start-0 ps-0" placeholder="Search address, landmark, or area (e.g. Aeon Mall, Toul Kork, BKK1)..." autocomplete="off" onkeydown="if(event.key==='Enter'){event.preventDefault();searchLocationOnMap();}">
                        <button type="button" class="btn btn-success fw-semibold px-3" onclick="searchLocationOnMap()" id="btnMapSearch">
                            <span id="mapSearchSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                            Search
                        </button>
                    </div>
                    <div id="mapSearchFeedback" class="small text-muted mt-1"></div>
                </div>

                <!-- MAP CONTAINER -->
                <div id="addressMap" style="height: 280px; width: 100%; border-radius: 12px; margin-bottom: 16px; border: 1px solid #e2e8f0;"></div>

                <div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 small mb-3">
                    <div>
                        <i class="bi bi-truck text-success me-1"></i>
                        <span id="modalDistanceCalc">Distance from store: <strong>Calculating...</strong></span>
                    </div>
                    <div class="badge bg-success-subtle text-success" id="modalEtaBadge">ETA: ~35 mins</div>
                </div>

                <form id="newAddressForm">
                    @csrf
                    <input type="hidden" name="latitude" id="modalLat" value="11.5564">
                    <input type="hidden" name="longitude" id="modalLng" value="104.9282">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Label <span class="text-danger">*</span></label>
                            <select name="label" class="form-select rounded-3" required>
                                <option value="Home">Home</option>
                                <option value="Work">Work</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Recipient Name <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" id="modalRecipientName" class="form-control rounded-3 bg-light text-muted fw-semibold" value="{{ auth()->user()->name }}" readonly required style="cursor: not-allowed;" title="Recipient name automatically follows your account name and cannot be changed.">
                            <div class="form-text text-muted" style="font-size: .72rem;">
                                <i class="bi bi-lock-fill text-secondary me-1"></i>Follows user name (Cannot change)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Contact Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="modalPhone" class="form-control rounded-3 font-monospace" value="{{ preg_replace('/[^0-9]/', '', auth()->user()->phone ?? '') }}" placeholder="012xxxxxx (9d) or 088/096 (10d)" maxlength="10" inputmode="numeric" oninput="handlePhoneInput(this, document.getElementById('modalPhoneCounter'))" required>
                            <div class="form-text text-muted d-flex justify-content-between" style="font-size: .72rem;">
                                <span><i class="bi bi-shield-check text-success me-1"></i>088/096=10d, other=9d</span>
                                <span id="modalPhoneCounter" class="fw-semibold">0/9 digits</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Detailed Address Line <span class="text-danger">*</span></label>
                            <textarea name="address_line" id="modalAddressLine" class="form-control rounded-3" rows="2" placeholder="Street name, building name, room/floor, landmark..." required></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="modalDefaultCheck" checked>
                                <label class="form-check-label small" for="modalDefaultCheck">
                                    Set as default delivery address
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top py-3 px-4">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-semibold" onclick="saveNewAddress()">Save & Use Address</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.cursor-pointer { cursor: pointer; }
.address-select-card {
    transition: all .15s ease;
}
.address-select-card:hover {
    border-color: #16a34a !important;
}
.payment-method-card {
    transition: all .15s ease;
}
.payment-method-card:hover {
    border-color: #16a34a !important;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
let currentSubtotal = {{ $totalAmount }};
let currentDiscount = 0;
let appliedPromoCode = '';
let currentPaymentMethod = 'card';
let stripeInstance = null;
let stripeElements = null;
let map = null;
let mapMarker = null;

const storeLat = 11.5564;
const storeLng = 104.9282;

document.addEventListener('DOMContentLoaded', async function () {
    initLeafletMapModal();
    initStripe();
});

/* ========================================================
   1. LEAFLET MAP & PIN-DROP
======================================================== */
function initLeafletMapModal() {
    const modalEl = document.getElementById('addressModal');
    modalEl.addEventListener('shown.bs.modal', function () {
        if (!map) {
            let startLat = storeLat;
            let startLng = storeLng;

            map = L.map('addressMap').setView([startLat, startLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Custom green marker icon
            const markerIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            mapMarker = L.marker([startLat, startLng], {
                draggable: true,
                icon: markerIcon
            }).addTo(map);

            mapMarker.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                updatePinLocation(pos.lat, pos.lng);
            });

            map.on('click', function (e) {
                mapMarker.setLatLng(e.latlng);
                updatePinLocation(e.latlng.lat, e.latlng.lng);
            });

            updatePinLocation(startLat, startLng);
        } else {
            map.invalidateSize();
        }
        const modalPhoneEl = document.getElementById('modalPhone');
        if (modalPhoneEl) {
            handlePhoneInput(modalPhoneEl, document.getElementById('modalPhoneCounter'));
        }
    });
}

function updatePinLocation(lat, lng) {
    document.getElementById('modalLat').value = lat;
    document.getElementById('modalLng').value = lng;

    fetch(`{{ route('address.distance') }}?latitude=${lat}&longitude=${lng}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalDistanceCalc').innerHTML = `Distance from store: <strong>${data.distance_km} km</strong>`;
            document.getElementById('modalEtaBadge').innerText = `ETA: ~${data.eta_minutes} mins`;
        })
        .catch(console.error);
}

/* ========================================================
   LOCATION SEARCH ON MAP
======================================================== */
function searchLocationOnMap() {
    if (!map || !mapMarker) return;
    const input = document.getElementById('mapSearchInput');
    const query = input ? input.value.trim() : '';
    const feedback = document.getElementById('mapSearchFeedback');
    const spinner = document.getElementById('mapSearchSpinner');
    const btn = document.getElementById('btnMapSearch');

    if (!query) {
        if (feedback) feedback.innerHTML = '<span class="text-danger fw-semibold">Please enter an address or landmark name to search.</span>';
        return;
    }

    if (feedback) feedback.innerHTML = '<span class="text-muted">Searching map...</span>';
    if (spinner) spinner.classList.remove('d-none');
    if (btn) btn.disabled = true;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`)
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.classList.add('d-none');
            if (btn) btn.disabled = false;

            if (data && data.length > 0) {
                const item = data[0];
                const lat = parseFloat(item.lat);
                const lon = parseFloat(item.lon);

                mapMarker.setLatLng([lat, lon]);
                map.flyTo([lat, lon], 16, { duration: 1.2 });
                updatePinLocation(lat, lon);

                // Auto-fill address line
                const addrLine = document.getElementById('modalAddressLine');
                if (addrLine) {
                    addrLine.value = item.display_name;
                }

                if (feedback) {
                    const shortName = item.display_name.split(',').slice(0, 3).join(',');
                    feedback.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-geo-alt-fill me-1"></i>Found: ${shortName} (Pin dropped & distance calculated)</span>`;
                }
            } else {
                if (feedback) feedback.innerHTML = '<span class="text-warning fw-semibold">No location found matching that search. You can also drag the pin directly on the map.</span>';
            }
        })
        .catch(() => {
            if (spinner) spinner.classList.add('d-none');
            if (btn) btn.disabled = false;
            if (feedback) feedback.innerHTML = '<span class="text-danger">Location search service temporarily unavailable. You can drag the pin manually.</span>';
        });
}

function handlePhoneInput(input, counterEl) {
    let val = input.value.replace(/[^0-9]/g, '');

    // 088 and 096 need 10 digits; all other prefixes need 9 digits
    const isTenDigit = val.startsWith('088') || val.startsWith('096');
    const targetLength = isTenDigit ? 10 : 9;

    if (val.length > targetLength) {
        val = val.slice(0, targetLength);
    }
    input.value = val;
    input.maxLength = targetLength;

    if (counterEl) {
        counterEl.innerText = `${val.length}/${targetLength} digits`;
        if (val.length === targetLength && val.startsWith('0')) {
            counterEl.className = 'fw-bold text-success';
        } else {
            counterEl.className = 'fw-semibold text-muted';
        }
    }
}

function validatePhoneNumber(phone) {
    const clean = (phone || '').replace(/[^0-9]/g, '');

    if (!clean.startsWith('0')) {
        return { valid: false, message: 'Phone number must start with 0.' };
    }

    if (clean.startsWith('088') || clean.startsWith('096')) {
        if (clean.length !== 10) {
            return {
                valid: false,
                message: `Phone numbers starting with ${clean.slice(0, 3)} must be exactly 10 digits (currently ${clean.length}).`
            };
        }
    } else {
        if (clean.length !== 9) {
            return {
                valid: false,
                message: `Phone numbers must be exactly 9 digits for this operator (088 and 096 require 10 digits). Currently ${clean.length} digits.`
            };
        }
    }

    return { valid: true, clean: clean };
}

function saveNewAddress() {
    const form = document.getElementById('newAddressForm');
    const phoneInput = document.getElementById('modalPhone');
    const rawPhone = phoneInput ? phoneInput.value : '';
    const phoneCheck = validatePhoneNumber(rawPhone);

    if (!phoneCheck.valid) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Phone Number',
            text: phoneCheck.message
        });
        if (phoneInput) phoneInput.focus();
        return;
    }

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    formData.set('phone', phoneCheck.clean); // ensure clean numeric digits

    fetch('{{ route('address.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.message || 'Could not save address.'});
        }
    })
    .catch(err => {
        Swal.fire({icon: 'error', title: 'Error', text: 'Failed to connect to server.'});
    });
}

/* ========================================================
   2. SAVE QUICK PHONE
======================================================== */
function saveQuickPhone() {
    const rawPhone = document.getElementById('quickPhoneInput').value;
    const phoneCheck = validatePhoneNumber(rawPhone);
    const msgBox = document.getElementById('phoneAlertMsg');

    if (!phoneCheck.valid) {
        msgBox.className = 'small mt-2 text-danger fw-semibold';
        msgBox.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-1"></i>${phoneCheck.message}`;
        msgBox.classList.remove('d-none');
        return;
    }

    fetch('{{ route('profile.update', auth()->id()) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'PATCH'
        },
        body: JSON.stringify({
            name: @json(auth()->user()->name),
            email: @json(auth()->user()->email),
            phone: phoneCheck.clean
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('phoneRequiredAlert').classList.add('d-none');
        Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Phone saved!', showConfirmButton: false, timer: 2000});
        window.location.reload();
    })
    .catch(() => {
        // Form post fallback
        window.location.reload();
    });
}

/* ========================================================
   3. ADDRESS SELECTION & ETA UPDATES
======================================================== */
function selectAddressCard(radio) {
    document.querySelectorAll('.address-select-card').forEach(card => {
        card.classList.remove('border-success', 'bg-success-subtle', 'bg-opacity-10');
    });
    radio.closest('.address-select-card').classList.add('border-success', 'bg-success-subtle', 'bg-opacity-10');

    const eta = radio.getAttribute('data-eta');
    if (eta) {
        document.getElementById('etaDisplay').innerText = `~${eta} mins`;
    }
}

/* ========================================================
   4. PAYMENT METHOD TOGGLE
======================================================== */
function togglePaymentMethod(method) {
    currentPaymentMethod = method;

    const cardLabel = document.getElementById('cardMethodLabel');
    const codLabel = document.getElementById('codMethodLabel');
    const cardSection = document.getElementById('cardPaymentSection');
    const codSection = document.getElementById('codPaymentSection');

    if (method === 'card') {
        cardLabel.classList.add('border-success', 'bg-success-subtle', 'bg-opacity-10');
        codLabel.classList.remove('border-success', 'bg-success-subtle', 'bg-opacity-10');
        cardSection.classList.remove('d-none');
        codSection.classList.add('d-none');
    } else {
        codLabel.classList.add('border-success', 'bg-success-subtle', 'bg-opacity-10');
        cardLabel.classList.remove('border-success', 'bg-success-subtle', 'bg-opacity-10');
        cardSection.classList.add('d-none');
        codSection.classList.remove('d-none');
    }
}

/* ========================================================
   5. STRIPE SETUP
======================================================== */
async function initStripe() {
    const stripeKey = @json($stripeKey);
    if (!stripeKey) return;

    try {
        stripeInstance = Stripe(stripeKey);
        const response = await fetch('{{ route('payment.intent') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        if (response.ok && data.clientSecret) {
            stripeElements = stripeInstance.elements({ clientSecret: data.clientSecret, appearance: { theme: 'stripe' } });
            const paymentElement = stripeElements.create('payment');
            paymentElement.mount('#payment-element');
        }
    } catch (e) {
        console.error('Stripe initialization error:', e);
    }
}

/* ========================================================
   6. PROMO CODE APPLICATION
======================================================== */
function applyPromoCode() {
    const code = document.getElementById('promoCodeInput').value.trim();
    const msg = document.getElementById('promoMsg');

    if (!code) {
        msg.className = 'small mt-1 text-danger';
        msg.innerText = 'Please enter a code.';
        msg.classList.remove('d-none');
        return;
    }

    fetch('{{ route('promo.apply') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ code: code, amount: currentSubtotal })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentDiscount = data.discount_amount;
            appliedPromoCode = data.promo_code;

            document.getElementById('discountRow').classList.remove('d-none');
            document.getElementById('discountBadge').innerText = data.discount_label;
            document.getElementById('discountAmountText').innerText = `-$${data.discount_amount.toFixed(2)}`;
            document.getElementById('finalTotalText').innerText = `$${data.new_total.toFixed(2)}`;
            document.getElementById('btnAmountText').innerText = `$${data.new_total.toFixed(2)}`;

            msg.className = 'small mt-1 text-success';
            msg.innerText = data.message;
            msg.classList.remove('d-none');
        } else {
            msg.className = 'small mt-1 text-danger';
            msg.innerText = data.message || 'Invalid promo code.';
            msg.classList.remove('d-none');
        }
    })
    .catch(() => {
        msg.className = 'small mt-1 text-danger';
        msg.innerText = 'Could not verify promo code.';
        msg.classList.remove('d-none');
    });
}

/* ========================================================
   7. SUBMIT ORDER
======================================================== */
async function submitOrder() {
    const errorBox = document.getElementById('payment-error');
    const btn = document.getElementById('pay-button');
    const spinner = document.getElementById('button-spinner');
    const btnText = document.getElementById('button-text');

    errorBox.classList.add('d-none');

    // 1. Check phone
    const userPhone = @json(auth()->user()->phone);
    if (!userPhone) {
        errorBox.textContent = 'Phone number is required in your profile before placing an order. Please save your phone above.';
        errorBox.classList.remove('d-none');
        document.getElementById('quickPhoneInput').focus();
        return;
    }

    // 2. Check location
    const selectedRadio = document.querySelector('input[name="selected_address_id"]:checked');
    if (!selectedRadio) {
        errorBox.textContent = 'Please select or add a delivery location on the map before proceeding.';
        errorBox.classList.remove('d-none');
        return;
    }

    const deliveryAddress = selectedRadio.getAttribute('data-address');
    const customerPhone = selectedRadio.getAttribute('data-phone') || userPhone;
    const deliveryLat = selectedRadio.getAttribute('data-lat');
    const deliveryLng = selectedRadio.getAttribute('data-lng');
    const distanceKm = selectedRadio.getAttribute('data-dist');
    const etaMinutes = selectedRadio.getAttribute('data-eta');
    const deliveryNotes = document.getElementById('delivery_notes').value.trim();

    btn.disabled = true;
    spinner.classList.remove('d-none');
    btnText.classList.add('d-none');

    // 3. Process Card vs COD
    if (currentPaymentMethod === 'card') {
        if (!stripeInstance || !stripeElements) {
            errorBox.textContent = 'Stripe payment could not be loaded. Please refresh or choose Cash on Delivery.';
            errorBox.classList.remove('d-none');
            btn.disabled = false;
            spinner.classList.add('d-none');
            btnText.classList.remove('d-none');
            return;
        }

        const result = await stripeInstance.confirmPayment({
            elements: stripeElements,
            redirect: 'if_required',
            confirmParams: { return_url: @json(route('cart.checkout')) }
        });

        if (result.error) {
            errorBox.textContent = result.error.message || 'Payment failed.';
            errorBox.classList.remove('d-none');
            btn.disabled = false;
            spinner.classList.add('d-none');
            btnText.classList.remove('d-none');
            return;
        }

        if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
            submitCompletionForm({
                payment_method: 'card',
                payment_intent_id: result.paymentIntent.id,
                delivery_address: deliveryAddress,
                customer_phone: customerPhone,
                delivery_notes: deliveryNotes,
                promo_code: appliedPromoCode,
                delivery_latitude: deliveryLat,
                delivery_longitude: deliveryLng,
                distance_km: distanceKm,
                eta_minutes: etaMinutes
            });
        } else {
            errorBox.textContent = 'Payment could not be completed.';
            errorBox.classList.remove('d-none');
            btn.disabled = false;
            spinner.classList.add('d-none');
            btnText.classList.remove('d-none');
        }
    } else {
        // Cash on Delivery
        submitCompletionForm({
            payment_method: 'cod',
            delivery_address: deliveryAddress,
            customer_phone: customerPhone,
            delivery_notes: deliveryNotes,
            promo_code: appliedPromoCode,
            delivery_latitude: deliveryLat,
            delivery_longitude: deliveryLng,
            distance_km: distanceKm,
            eta_minutes: etaMinutes
        });
    }
}

function submitCompletionForm(params) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = @json(route('checkout.complete'));

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    for (const key in params) {
        if (params[key] !== null && params[key] !== undefined) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
