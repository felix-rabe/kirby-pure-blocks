document.addEventListener("DOMContentLoaded", function () {

  // --- Lazy-load self-hosted videos ---
  var lazyVideos = [].slice.call(document.querySelectorAll("video[data-src]"));

  if ("IntersectionObserver" in window) {
    var videoObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var video = entry.target;

          Array.from(video.children).forEach(function (source) {
            if (source.tagName === "SOURCE" && source.dataset.src) {
              source.src = source.dataset.src;
            }
          });

          if (video.dataset.src) video.src = video.dataset.src;
          if (video.dataset.poster) video.poster = video.dataset.poster;

          video.load();
          video.classList.remove("lazy");
          videoObserver.unobserve(video);
        }
      });
    });

    lazyVideos.forEach(function (video) {
      videoObserver.observe(video);
    });
  } else {
    lazyVideos.forEach(function (video) {
      Array.from(video.children).forEach(function (source) {
        if (source.tagName === "SOURCE" && source.dataset.src) {
          source.src = source.dataset.src;
        }
      });

      if (video.dataset.src) video.src = video.dataset.src;
      if (video.dataset.poster) video.poster = video.dataset.poster;

      video.load();
      video.classList.remove("lazy");
    });
  }

  // --- Hide pseudo-thumbhash when self-hosted video loads ---
  document.querySelectorAll("video[data-src]").forEach(function (video) {
    video.addEventListener("loadeddata", function () {
      const figure = video.closest("figure");

      if (figure) {
        const thumbhash = figure.querySelector(".pseudo-thumbhash");

        if (thumbhash) {
          thumbhash.remove();
        }
      }
    });
  });

  // --- External videos: button-only click ---
  document.querySelectorAll(".pure-video__consent-button").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.stopPropagation();

      var iframeSrc = btn.dataset.src;

      if (!iframeSrc) return;

      var iframe = document.createElement("iframe");

      iframe.src = iframeSrc;
      iframe.frameBorder = 0;
      iframe.allow = "autoplay; encrypted-media; picture-in-picture";
      iframe.allowFullscreen = true;
      iframe.style.position = "absolute";
      iframe.style.top = 0;
      iframe.style.left = 0;
      iframe.style.width = "100%";
      iframe.style.height = "100%";

      // Hide pseudo-thumbhash on click (external video)
      const figure = btn.closest("figure");

      if (figure) {
        const thumbhash = figure.querySelector(".pseudo-thumbhash");

        if (thumbhash) {
          thumbhash.remove();
        }
      }

      btn.closest(".pure-video__wrapper").replaceChild(
        iframe,
        btn.closest(".pure-video__consent")
      );
    });
  });

});