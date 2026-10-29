<?php
session_start();

include 'config/db.php';
require_once 'secure_helpers.php';

$user_id = ecom_require_user();

$return = $_GET['return'] ?? 'profile';
$edit_id = (int)($_GET['id'] ?? 0);

$backUrl = 'user_profile.php?tab=addresses';

if ($return === 'checkout') {
    $backUrl = 'checkout.php';
}

/* =========================
   SAVE / UPDATE ADDRESS
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $id         = (int)($_POST['id'] ?? 0);
    $name       = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $phone      = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $pincode    = mysqli_real_escape_string($conn, trim($_POST['pincode'] ?? ''));
    $city       = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
    $state      = mysqli_real_escape_string($conn, trim($_POST['state'] ?? ''));
    $type       = mysqli_real_escape_string($conn, trim($_POST['type'] ?? 'Home'));
    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if ($name === '' || $phone === '' || $pincode === '' || $city === '' || $state === '') {
        echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
        exit;
    }

    if (!preg_match('/^[0-9]{6}$/', $pincode)) {
        echo json_encode(['status' => 'error', 'message' => 'Enter valid 6 digit pincode']);
        exit;
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Enter valid 10 digit phone number']);
        exit;
    }

    if ($is_default) {
        mysqli_query($conn, "UPDATE customer_addresses SET is_default = 0 WHERE customer_id = '$user_id'");
    }

    if ($id > 0) {
        $q = mysqli_query($conn, "
            UPDATE customer_addresses SET
                name = '$name',
                phone = '$phone',
                city = '$city',
                state = '$state',
                pincode = '$pincode',
                type = '$type',
                is_default = '$is_default'
            WHERE id = '$id'
            AND customer_id = '$user_id'
        ");
    } else {
        $q = mysqli_query($conn, "
            INSERT INTO customer_addresses
            (customer_id, name, phone, city, state, pincode, type, is_default)
            VALUES
            ('$user_id', '$name', '$phone', '$city', '$state', '$pincode', '$type', '$is_default')
        ");
    }

    echo json_encode(
        $q
            ? ['status' => 'success']
            : ['status' => 'error', 'message' => mysqli_error($conn)]
    );

    exit;
}

$user = ecom_get_user($conn, $user_id);

$editAddress = null;

if ($edit_id > 0) {
    $editAddress = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM customer_addresses 
        WHERE id = '$edit_id'
        AND customer_id = '$user_id'
        LIMIT 1
    "));
}

include 'header.php';
?>

<section class="breadcrumb-section">
    <h1><?= $editAddress ? 'EDIT ADDRESS' : 'ADD ADDRESS'; ?></h1>
    <p class="breadcrumb">
        <a href="index.php">Home</a> <span>| Address</span>
    </p>
</section>

<div class="container my-5 manage-address-simple">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="ecom-card address-form-card">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="ecom-title mb-1">
                            <?= $editAddress ? 'Edit Address' : 'Add New Address'; ?>
                        </h4>
                        <p class="text-muted mb-0">Fill delivery address details.</p>
                    </div>

                    <a href="<?= htmlspecialchars($backUrl); ?>" class="btn btn-outline-secondary btn-sm">
                        Back
                    </a>
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="useCurrentLocation()">
                        📍 Use Current Location
                    </button>
                </div>

                <form id="addressForm">
                    <input type="hidden" name="id" id="edit_id" value="<?= (int)($editAddress['id'] ?? 0); ?>">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input
                                class="form-control"
                                name="name"
                                id="ad_name"
                                placeholder="Full Name"
                                value="<?= ecom_h($editAddress['name'] ?? ($user['name'] ?? '')); ?>"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input
                                class="form-control"
                                name="phone"
                                id="ad_phone"
                                placeholder="Phone"
                                maxlength="10"
                                inputmode="numeric"
                                pattern="[0-9]{10}"
                                value="<?= ecom_h($editAddress['phone'] ?? ($user['mobile'] ?? '')); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pincode</label>
                            <input
                                class="form-control"
                                name="pincode"
                                id="pincode"
                                placeholder="Pincode"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                value="<?= ecom_h($editAddress['pincode'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">District / City</label>
                            <input
                                class="form-control"
                                name="city"
                                id="district"
                                placeholder="District / City"
                                value="<?= ecom_h($editAddress['city'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">State</label>
                            <input
                                class="form-control"
                                name="state"
                                id="state"
                                placeholder="State"
                                value="<?= ecom_h($editAddress['state'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address Type</label>
                            <?php $currentType = $editAddress['type'] ?? 'Home'; ?>

                            <select class="form-select" name="type" id="ad_type">
                                <option value="Home" <?= $currentType === 'Home' ? 'selected' : ''; ?>>Home</option>
                                <option value="Work" <?= $currentType === 'Work' ? 'selected' : ''; ?>>Work</option>
                            </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <label class="form-check d-flex align-items-center gap-2 mb-0">
                                <input
                                    class="form-check-input m-0"
                                    type="checkbox"
                                    name="is_default"
                                    id="makeDefault"
                                    value="1"
                                    <?= !empty($editAddress['is_default']) ? 'checked' : ''; ?>>

                                <span>Make this my default address</span>
                            </label>
                        </div>

                        <div class="col-12 d-flex align-items-center gap-2 flex-wrap">
                            <button class="btn btn-success" type="submit">
                                <?= $editAddress ? 'Update Address' : 'Save Address'; ?>
                            </button>

                            <button class="btn btn-outline-secondary" type="button" onclick="resetAddressForm()">
                                Reset
                            </button>

                            <span id="addressMsg" class="fw-semibold"></span>
                        </div>

                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

<style>
    .address-form-card {
        border-radius: 18px;
        padding: 24px;
    }

    .address-form-card .form-control,
    .address-form-card .form-select {
        min-height: 48px;
        border-radius: 10px;
    }

    .address-form-card .form-control:focus,
    .address-form-card .form-select:focus {
        border-color: #ff5a1f;
        box-shadow: 0 0 0 0.15rem rgba(255, 90, 31, 0.15);
    }
</style>

<script>
    const returnUrl = "<?= htmlspecialchars($backUrl, ENT_QUOTES); ?>";

    const addressForm = document.getElementById('addressForm');
    const pincodeInput = document.getElementById('pincode');
    const districtInput = document.getElementById('district');
    const stateInput = document.getElementById('state');
    const phoneInput = document.getElementById('ad_phone');
    const addressMsg = document.getElementById('addressMsg');

    let lastFetchedPincode = '';
    let pincodeTimer = null;

    function setAddressMsg(message, color = 'red') {
        if (!addressMsg) return;
        addressMsg.innerText = message || '';
        addressMsg.style.color = color;
    }

    function resetAddressForm() {
        addressForm.reset();

        <?php if (!$editAddress): ?>
            document.getElementById('edit_id').value = '';
        <?php endif; ?>

        lastFetchedPincode = '';
        setAddressMsg('');
    }

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    }

    if (pincodeInput) {
        pincodeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);

            clearTimeout(pincodeTimer);

            if (this.value.length === 6) {
                pincodeTimer = setTimeout(() => fetchByPincode(this.value), 250);
            } else {
                lastFetchedPincode = '';
            }
        });

        pincodeInput.addEventListener('blur', function() {
            const pin = this.value.trim();
            if (pin.length === 6) {
                fetchByPincode(pin);
            }
        });
    }

    addressForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const phone = phoneInput.value.trim();
        const pincode = pincodeInput.value.trim();

        if (!/^[0-9]{10}$/.test(phone)) {
            setAddressMsg('Enter valid 10 digit phone number', 'red');
            phoneInput.focus();
            return;
        }

        if (!/^[0-9]{6}$/.test(pincode)) {
            setAddressMsg('Enter valid 6 digit pincode', 'red');
            pincodeInput.focus();
            return;
        }

        fetch('manage-addresses.php?return=<?= htmlspecialchars($return, ENT_QUOTES); ?>', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (typeof ecomToast === 'function') {
                        ecomToast('Address saved');
                    }
                    location.href = returnUrl;
                } else {
                    setAddressMsg(data.message || 'Something went wrong', 'red');
                }
            })
            .catch(() => setAddressMsg('Something went wrong', 'red'));
    });

    async function fetchByPincode(pin = '') {
        const pincode = (pin || pincodeInput.value || '').trim();

        if (!/^[0-9]{6}$/.test(pincode)) return;

        if (pincode === lastFetchedPincode && districtInput.value.trim() && stateInput.value.trim()) return;

        lastFetchedPincode = pincode;
        setAddressMsg('Fetching district and state...', '#ff5a1f');
        
        const oldDistrict = districtInput.value;
        const oldState = stateInput.value;
        districtInput.value = '';
        stateInput.value = '';

        try {
            let dataFetched = false;
            
            try {
                const response = await fetch('https://nominatim.openstreetmap.org/search?postalcode=' + encodeURIComponent(pincode) + '&country=India&format=json&addressdetails=1');
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const address = data[0].address || {};
                    const district = address.state_district || address.county || address.city || address.town || address.village || '';
                    const state = address.state || '';
                    
                    if (district || state) {
                        districtInput.value = district;
                        stateInput.value = state;
                        dataFetched = true;
                    }
                }
            } catch (e) {
                console.error("Nominatim API Error:", e);
            }

            if (!dataFetched) {
                try {
                    const response = await fetch('https://api.postalpincode.in/pincode/' + encodeURIComponent(pincode));
                    const data = await response.json();
                    if (data && data[0] && data[0].Status === 'Success' && data[0].PostOffice && data[0].PostOffice.length > 0) {
                        const postOffice = data[0].PostOffice[0];
                        districtInput.value = postOffice.District || postOffice.Block || postOffice.Region || '';
                        stateInput.value = postOffice.State || '';
                        dataFetched = true;
                    }
                } catch (e) {
                    console.error("Postal API Error:", e);
                }
            }

            if (!dataFetched) {
                try {
                    const res2 = await fetch('https://api.zippopotam.us/in/' + encodeURIComponent(pincode));
                    if (res2.ok) {
                        const data2 = await res2.json();
                        if (data2 && data2.places && data2.places.length > 0) {
                            districtInput.value = data2.places[0]['state abbreviation'] === 'DL' ? data2.places[0]['state'] : data2.places[0]['place name'] || '';
                            stateInput.value = data2.places[0]['state'] || '';
                            dataFetched = true;
                        }
                    }
                } catch (e2) {
                    console.error("Zippopotamus API Error:", e2);
                }
            }

            if (dataFetched) {
                setAddressMsg('District and state filled successfully.', 'green');
            } else {
                lastFetchedPincode = '';
                districtInput.value = oldDistrict;
                stateInput.value = oldState;
                setAddressMsg('Invalid pincode or API unavailable. Please enter manually.', 'red');
            }
        } catch (error) {
            lastFetchedPincode = '';
            districtInput.value = oldDistrict;
            stateInput.value = oldState;
            setAddressMsg('Unable to fetch district/state. Please check internet or enter manually.', 'red');
        }
    }

    function useCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation not supported by your browser');
            return;
        }

        setAddressMsg('Fetching current location...', '#ff5a1f');

        navigator.geolocation.getCurrentPosition(
            position => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                    .then(res => res.json())
                    .then(data => {
                        const address = data.address || {};
                        const fetchedPincode = (address.postcode || '').replace(/[^0-9]/g, '').slice(0, 6);
                        const fetchedDistrict = address.city || address.town || address.village || address.county || address.state_district || '';
                        const fetchedState = address.state || '';
                        
                        pincodeInput.value = fetchedPincode;
                        if(fetchedDistrict) districtInput.value = fetchedDistrict;
                        if(fetchedState) stateInput.value = fetchedState;

                        if (fetchedPincode.length === 6) {
                            if (!districtInput.value || !stateInput.value) {
                                fetchByPincode(fetchedPincode);
                            } else {
                                lastFetchedPincode = fetchedPincode;
                                setAddressMsg('Location fetched.', 'green');
                            }
                        } else {
                            setAddressMsg('Location fetched. Please enter pincode manually.', 'green');
                        }
                    })
                    .catch(() => setAddressMsg('Unable to fetch current location details.', 'red'));
            },
            () => setAddressMsg('Please allow location permission.', 'red')
        );
    }
</script>

<?php include 'footer.php'; ?>