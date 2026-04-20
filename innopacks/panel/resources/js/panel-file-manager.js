/**
 * File Manager iframe picker for panel forms.
 * Opens the file manager in a layer.js iframe popup and returns selected files via callback.
 */
export default {
  init: function(callback, options = {}) {
    const defaultOptions = {
      type: "image",
      multiple: false,
    };

    const finalOptions = { ...defaultOptions, ...options };

    window.fileManagerCallback = function(file) {
      if (typeof callback === "function") {
        callback(file);
      }
    };

    layer.open({
      type: 2,
      title: urls.file_manager_title || "File Manager",
      shadeClose: false,
      shade: 0.8,
      area: ["90%", "90%"],
      content: `${urls.panel_base}/file_manager/iframe?type=${finalOptions.type}&multiple=${finalOptions.multiple ? "1" : "0"}`,
    });
  }
};
