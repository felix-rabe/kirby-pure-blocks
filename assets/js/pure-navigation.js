// Pure Navigation
(() => {
  function initPureNavigation() {
    const navigations = document.querySelectorAll(".pure-navigation");

    if (!navigations.length) return;

    navigations.forEach((navigation) => {
      const parent = navigation.closest("header") || navigation;
      const toggle = navigation.querySelector(".pure-navigation__toggle");

      const isMobile =
        navigation.classList.contains("pure-navigation--mobile");

      const isSticky =
        navigation.classList.contains("pure-navigation--sticky");

      const hideOnScroll =
        navigation.classList.contains("pure-navigation--hide-on-scroll");


      /*
       * Sticky / parent behavior
       */

      if (isSticky) {
        parent.classList.add("pure-navigation-parent--sticky");
      }

      if (hideOnScroll) {
        parent.classList.add("pure-navigation-parent--hide-on-scroll");
      }


      /*
       * Mobile menu
       */

      const closeMenu = () => {
        navigation.classList.remove("pure-navigation--open");

        if (toggle) {
          toggle.setAttribute("aria-expanded", "false");
          toggle.setAttribute("aria-label", "Open menu");
        }

        document.documentElement.classList.remove(
          "has-open-navigation"
        );

        parent.classList.remove("has-open-navigation");
      };

      const openMenu = () => {
        navigation.classList.add("pure-navigation--open");

        if (toggle) {
          toggle.setAttribute("aria-expanded", "true");
          toggle.setAttribute("aria-label", "Close menu");
        }

        document.documentElement.classList.add(
          "has-open-navigation"
        );

        parent.classList.add("has-open-navigation");

        // Never hide the header while the mobile menu is open
        parent.classList.remove("is-hidden");
      };

      if (isMobile && toggle) {
        toggle.addEventListener("click", () => {
          if (
            navigation.classList.contains(
              "pure-navigation--open"
            )
          ) {
            closeMenu();
          } else {
            openMenu();
          }
        });

        navigation
          .querySelectorAll(".pure-navigation__menu a")
          .forEach((link) => {
            link.addEventListener("click", closeMenu);
          });

        window.addEventListener("resize", () => {
          if (window.innerWidth >= 992) {
            closeMenu();
          }
        });

        document.addEventListener("keydown", (event) => {
          if (
            event.key === "Escape" &&
            navigation.classList.contains(
              "pure-navigation--open"
            )
          ) {
            closeMenu();
          }
        });
      }


      /*
       * Hide on scroll
       */

      if (!hideOnScroll) return;

      let lastScrollY = window.scrollY;
      let ticking = false;
      let ignoreAnchorScroll = false;
      let anchorScrollEndTimer = null;

      function isSamePageAnchor(link) {
        if (!link || !link.href) return false;

        const url = new URL(
          link.href,
          window.location.href
        );

        return (
          url.origin === window.location.origin &&
          url.pathname === window.location.pathname &&
          url.search === window.location.search &&
          url.hash.length > 1
        );
      }

      document.addEventListener("click", (event) => {
        const link = event.target.closest("a[href]");

        if (!isSamePageAnchor(link)) return;

        ignoreAnchorScroll = true;

        if (anchorScrollEndTimer) {
          window.clearTimeout(anchorScrollEndTimer);
        }
      });

      const updateNavigation = () => {
        const currentScrollY = window.scrollY;
        const difference =
          currentScrollY - lastScrollY;

        const mobileMenuIsOpen =
          navigation.classList.contains(
            "pure-navigation--open"
          );

        if (mobileMenuIsOpen) {
          parent.classList.remove("is-hidden");
        } else if (!ignoreAnchorScroll) {
          if (currentScrollY <= 80) {
            parent.classList.remove("is-hidden");
          } else if (difference > 8) {
            parent.classList.add("is-hidden");
          } else if (difference < -8) {
            parent.classList.remove("is-hidden");
          }
        }

        lastScrollY = currentScrollY;
        ticking = false;
      };

      window.addEventListener(
        "scroll",
        () => {
          if (ignoreAnchorScroll) {
            if (anchorScrollEndTimer) {
              window.clearTimeout(
                anchorScrollEndTimer
              );
            }

            anchorScrollEndTimer =
              window.setTimeout(() => {
                ignoreAnchorScroll = false;
                lastScrollY = window.scrollY;
              }, 150);
          }

          if (!ticking) {
            window.requestAnimationFrame(
              updateNavigation
            );

            ticking = true;
          }
        },
        { passive: true }
      );
    });
  }


  /*
   * Init
   */

  if (document.readyState === "loading") {
    document.addEventListener(
      "DOMContentLoaded",
      initPureNavigation,
      { once: true }
    );
  } else {
    initPureNavigation();
  }
})();