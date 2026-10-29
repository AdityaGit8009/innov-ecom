const addressForm = document.getElementById("addressForm");
const fullName = document.getElementById("fullName");
const phone = document.getElementById("phone");
const pincode = document.getElementById("pincode");
const district = document.getElementById("district");
const state = document.getElementById("state");
const addressType = document.getElementById("addressType");
const defaultAddress = document.getElementById("defaultAddress");
const addressList = document.getElementById("addressList");

/* =========================
   Fetch saved addresses
========================= */
function fetchAddresses() {
  fetch("fetch_addresses.php")
    .then((res) => res.text())
    .then((html) => (addressList.innerHTML = html))
    .catch((err) => console.error("Failed to fetch addresses:", err));
}

/* =========================
   Save new address
========================= */
addressForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const formData = new FormData();
  formData.append("name", fullName.value.trim());
  formData.append("phone", phone.value.trim());
  formData.append("city", district.value.trim());
  formData.append("state", state.value.trim());
  formData.append("pincode", pincode.value.trim());
  formData.append("type", addressType.value);
  formData.append("is_default", defaultAddress.checked ? 1 : 0);
  formData.append("action", "save_address");

  fetch("checkout.php", { method: "POST", body: formData })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        fetchAddresses();
        addressForm.reset();
      } else {
        alert("Address save failed! " + (data.message || ""));
      }
    })
    .catch((err) => console.error("Error saving address:", err));
});

/* =========================
   Remove address
========================= */
function removeAddress(id) {
  if (!confirm("Are you sure you want to remove this address?")) return;

  fetch(`remove_address.php?id=${id}`, { method: "GET" })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") fetchAddresses();
      else alert("Failed to remove address!");
    })
    .catch((err) => console.error("Error removing address:", err));
}

/* =========================
   Pincode lookup
========================= */
function fetchByPincode() {
  const pin = pincode.value.trim();
  if (pin.length !== 6) return;

  fetch(`https://api.postalpincode.in/pincode/${pin}`)
    .then((res) => res.json())
    .then((data) => {
      if (data[0]?.Status === "Success") {
        district.value = data[0].PostOffice[0].District || "";
        state.value = data[0].PostOffice[0].State || "";
      }
    })
    .catch((err) => console.error("Pincode lookup failed:", err));
}

/* =========================
   Use current location
========================= */
function useCurrentLocation() {
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      const { latitude, longitude } = pos.coords;
      fetch(
        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`,
      )
        .then((res) => res.json())
        .then((data) => {
          const a = data.address || {};
          pincode.value = a.postcode || "";
          district.value = a.city || a.town || a.village || "";
          state.value = a.state || "";
        })
        .catch((err) => console.error("Reverse geocoding failed:", err));
    },
    () => alert("Location permission denied"),
  );
}

/* =========================
   Initial fetch
========================= */
fetchAddresses();
