"use strict";

(function () {
  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  ready(function () {
    // modal variables
    // modal variables
    const modal = document.querySelector("[data-modal]");
    const modalCloseBtn = document.querySelector("[data-modal-close]");
    const modalCloseOverlay = document.querySelector("[data-modal-overlay]");

    /*
|--------------------------------------------------------------------------
| SHOW MODAL ONLY ONE TIME
|--------------------------------------------------------------------------
*/

    if (modal) {
      // agar pehli baar open ho raha hai
      if (!sessionStorage.getItem("modalShown")) {
        modal.classList.remove("closed");

        sessionStorage.setItem("modalShown", "yes");
      } else {
        // dobara refresh par hidden rahe
        modal.classList.add("closed");
      }
    }

    /*
|--------------------------------------------------------------------------
| CLOSE MODAL
|--------------------------------------------------------------------------
*/

    function modalCloseFunc() {
      if (modal) {
        modal.classList.add("closed");
      }
    }

    if (modalCloseOverlay) {
      modalCloseOverlay.addEventListener("click", modalCloseFunc);
    }

    if (modalCloseBtn) {
      modalCloseBtn.addEventListener("click", modalCloseFunc);
    }
    // notification toast variables
    const notificationToast = document.querySelector("[data-toast]");
    const toastCloseBtn = document.querySelector("[data-toast-close]");
    if (toastCloseBtn && notificationToast) {
      toastCloseBtn.addEventListener("click", function () {
        notificationToast.classList.add("closed");
      });
    }

    // mobile menu / category offcanvas variables
    const overlay = document.querySelector("[data-overlay]");
    const mainMenuOpenBtn = document.querySelector("[data-main-menu-open]");
    const mainMobileMenu = document.querySelector("[data-main-mobile-menu]");
    const mainMenuCloseBtn = document.querySelector("[data-main-menu-close]");
    const categoryOpenBtns = document.querySelectorAll("[data-category-open]");
    const categoryMenu = document.querySelector("[data-category-menu]");
    const categoryCloseBtns = document.querySelectorAll(
      "[data-category-close]",
    );

    // keep mobile offcanvas panels closed on first load
    if (mainMobileMenu) mainMobileMenu.classList.remove("active");
    if (categoryMenu) categoryMenu.classList.remove("active");
    if (overlay) overlay.classList.remove("active");

    function openOffcanvas(panel) {
      if (!panel) return;
      if (mainMobileMenu && panel !== mainMobileMenu)
        mainMobileMenu.classList.remove("active");
      if (categoryMenu && panel !== categoryMenu)
        categoryMenu.classList.remove("active");
      panel.classList.add("active");
      if (overlay) overlay.classList.add("active");
      document.body.classList.add("sidebar-backdrop-lock");
    }

    function closeAllOffcanvas() {
      if (mainMobileMenu) mainMobileMenu.classList.remove("active");
      if (categoryMenu) categoryMenu.classList.remove("active");
      if (overlay) overlay.classList.remove("active");
      document.body.classList.remove("sidebar-backdrop-lock");
    }

    if (mainMenuOpenBtn)
      mainMenuOpenBtn.addEventListener("click", function () {
        openOffcanvas(mainMobileMenu);
      });
    if (mainMenuCloseBtn)
      mainMenuCloseBtn.addEventListener("click", closeAllOffcanvas);
    categoryOpenBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        openOffcanvas(categoryMenu);
      });
    });
    categoryCloseBtns.forEach(function (btn) {
      btn.addEventListener("click", closeAllOffcanvas);
    });
    if (overlay) overlay.addEventListener("click", closeAllOffcanvas);

    // Category accordion: only one category open at a time.
    // Event delegation fixes cases where script loads before/after dynamic PHP sidebar markup.
    document.addEventListener("click", function (event) {
      const btn = event.target.closest("[data-accordion-btn]");
      if (!btn) return;

      event.preventDefault();

      const currentPanel = btn.nextElementSibling;
      if (!currentPanel || !currentPanel.matches("[data-accordion]")) return;

      const sidebar = btn.closest("[data-category-menu]") || document;
      const alreadyOpen = currentPanel.classList.contains("active");

      sidebar.querySelectorAll("[data-accordion]").forEach(function (panel) {
        panel.classList.remove("active");
      });

      sidebar
        .querySelectorAll("[data-accordion-btn]")
        .forEach(function (button) {
          button.classList.remove("active");
          button.setAttribute("aria-expanded", "false");
        });

      if (!alreadyOpen) {
        currentPanel.classList.add("active");
        btn.classList.add("active");
        btn.setAttribute("aria-expanded", "true");
      }
    });

    // Global mobile category drawer accordion.
    document.addEventListener("click", function (event) {
      const btn = event.target.closest("[data-global-category-btn]");
      if (!btn) return;

      event.preventDefault();
      const parent = btn.closest(".global-category-item");
      const panel = parent
        ? parent.querySelector("[data-global-category-panel]")
        : null;
      if (!panel) return;

      const alreadyOpen = panel.classList.contains("active");
      document
        .querySelectorAll("[data-global-category-btn]")
        .forEach(function (item) {
          item.classList.remove("active");
          const icon = item.querySelector("ion-icon");
          if (icon) icon.setAttribute("name", "add-outline");
        });
      document
        .querySelectorAll("[data-global-category-panel]")
        .forEach(function (item) {
          item.classList.remove("active");
        });

      if (!alreadyOpen) {
        btn.classList.add("active");
        const icon = btn.querySelector("ion-icon");
        if (icon) icon.setAttribute("name", "remove-outline");
        panel.classList.add("active");
      }
    });

    // Swiper slider init only when Swiper and slider exist
    if (typeof Swiper !== "undefined" && document.querySelector(".mySwiper")) {
      new Swiper(".mySwiper", {
        slidesPerView: 1,
        autoplay: {
          delay: 2500,
          disableOnInteraction: false,
        },
        loop: true,
        spaceBetween: 10,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
          640: { slidesPerView: 2, spaceBetween: 20 },
          768: { slidesPerView: 3, spaceBetween: 40 },
          1024: { slidesPerView: 4, spaceBetween: 50 },
        },
      });
    }
  });
})();
