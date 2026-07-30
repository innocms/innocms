/**
 * Media Library iframe picker for panel forms.
 * Opens the media manager in a layer.js iframe popup and returns selected
 * files via callback. Exposed as window.inno.media.init, with
 * window.inno.fileManagerIframe kept as a back-compat alias.
 */
export default {
  init: function(callback, options = {}) {
    const defaultOptions = {
      type: "image",
      multiple: false,
    };

    const finalOptions = { ...defaultOptions, ...options };

    window.mediaCallback = function(file) {
      if (typeof callback === "function") {
        callback(file);
      }
    };

    layer.open({
      type: 2,
      title: urls.file_manager_title || "Media",
      shadeClose: false,
      shade: 0.8,
      area: ["90%", "90%"],
      content: `${urls.panel_base}/media/iframe?type=${finalOptions.type}&multiple=${finalOptions.multiple ? "1" : "0"}`,
    });
  }
};
