{{-- Unified AI content generation modal script. Exposes window.aiModal. --}}
<script>
const aiModal = {
  state: {
    column: '',
    field: '',
    entityType: '',
    entityId: 0,
    isRichText: false,
    isMultilingual: true,
    editorId: '',
    sourceLocale: '',
  },

  /**
   * Per-column prompt templates for simple mode.
   * User input is appended to the template to form the final prompt.
   */
  templates: {
    product_summary:       @json(__('aicore::modal.tpl_product_summary')),
    product_selling_point: @json(__('aicore::modal.tpl_product_selling_point')),
    product_slug:          @json(__('aicore::modal.tpl_product_slug')),
    product_title:         @json(__('aicore::modal.tpl_product_title')),
    product_description:   @json(__('aicore::modal.tpl_product_description')),
    product_keywords:      @json(__('aicore::modal.tpl_product_keywords')),
    product_content:       @json(__('aicore::modal.tpl_product_content')),
    article_summary:       @json(__('aicore::modal.tpl_article_summary')),
    article_slug:          @json(__('aicore::modal.tpl_article_slug')),
    article_title:         @json(__('aicore::modal.tpl_article_title')),
    article_description:   @json(__('aicore::modal.tpl_article_description')),
    article_keywords:      @json(__('aicore::modal.tpl_article_keywords')),
    article_content:       @json(__('aicore::modal.tpl_article_content')),
    category_content:      @json(__('aicore::modal.tpl_category_content')),
    brand_content:         @json(__('aicore::modal.tpl_brand_content')),
    page_content:          @json(__('aicore::modal.tpl_page_content')),
  },

  init() {
    const self = this;

    // Unified trigger for all AI buttons
    $(document).on("click", ".locale-ai-btn, .ai-generate, .rich-text-ai-btn", function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      self.openFromButton($(this));
    });

    // Mode toggle
    $(document).on("click", ".ai-modal-mode-btn", function () {
      const mode = $(this).data("mode");
      $(".ai-modal-mode-btn")
        .removeClass("active btn-primary")
        .addClass("btn-outline-secondary")
        .filter('[data-mode="' + mode + '"]')
        .addClass("active btn-primary")
        .removeClass("btn-outline-secondary");
      $("#aiModalFinalHint").toggle(mode === "simple");
      self.refreshFinalPrompt();
    });

    // Live preview
    $(document).on("input", "#aiModalPrompt", function () {
      self.refreshFinalPrompt();
    });

    // Generate
    $(document).on("click", "#aiModalGenerateBtn", function () {
      self.generate();
    });

    // Apply
    $(document).on("click", "#aiModalApplyBtn", function () {
      self.apply();
    });

    // Fix aria-hidden a11y warning: blur focused element before modal hides
    $(document).on("hide.bs.modal", "#aiGenerateModal", function () {
      const active = document.activeElement;
      if (active && typeof active.blur === "function" && !$(active).is("body")) {
        active.blur();
      }
    });
  },

  /**
   * Open modal based on data attributes on the triggering button.
   */
  openFromButton($btn) {
    const state = {
      column: $btn.data("column") || "",
      field: $btn.data("field") || "",
      entityType: $btn.data("entity-type") || "",
      entityId: parseInt($btn.data("entity-id"), 10) || 0,
      isRichText: $btn.hasClass("rich-text-ai-btn"),
      isMultilingual: true,
      editorId: $btn.data("el-id") || "",
      sourceLocale: "",
    };

    state.isMultilingual = !["product_slug", "article_slug"].includes(state.column);

    // Determine source locale
    const $wrapper = $btn.closest(".locale-field-wrapper");
    if ($wrapper.length) {
      state.sourceLocale = $wrapper.data("panel-locale") || "";
    } else if (state.isRichText && state.editorId) {
      const m = state.editorId.match(/^content-(.+)$/);
      state.sourceLocale = m ? m[1] : "";
    } else {
      // Try sibling input data-locale (rich-text in some panes)
      const $input = $btn.closest(".input-group, .form-row").find("input[data-locale], textarea[data-locale]");
      state.sourceLocale = $input.data("locale") || "";
    }

    this.openFromState(state);
  },

  /**
   * Open modal from an explicit state object (used by TinyMCE toolbar button).
   */
  openFromState(state) {
    Object.assign(this.state, {
      column: "",
      field: "",
      entityType: "",
      entityId: 0,
      isRichText: false,
      isMultilingual: true,
      editorId: "",
      sourceLocale: "",
    }, state);

    // Show/hide target language selector based on whether the field is multilingual
    $("#aiModalLocaleWrapper").toggle(this.state.isMultilingual);

    // Default target locale = source locale
    $("#aiModalLocale").val(this.state.sourceLocale || "");

    // Reset prompt area
    const tpl = this.templates[this.state.column] || @json(__('aicore::modal.tpl_default'));
    $("#aiModalPrompt").val("");
    this.setSimpleModeDefault(tpl);

    // Reset result
    $("#aiModalResult").val("").show();
    $("#aiModalResultMulti").empty().hide();

    // Show simple mode by default
    $(".ai-modal-mode-btn[data-mode='simple']").trigger("click");

    // Try to auto-fill prompt with current field value or product title from DOM
    this.prefillFromContext();

    new bootstrap.Modal($("#aiGenerateModal")[0]).show();
  },

  /**
   * Pre-fill prompt with current input value (best-effort).
   */
  prefillFromContext() {
    const $wrapper = $(".locale-field-wrapper").filter(function () {
      return $(this).find(".locale-ai-btn[data-column='" + aiModal.state.column + "']").length > 0;
    }).first();

    let currentValue = "";
    if ($wrapper.length) {
      currentValue = ($wrapper.find("> .input-group .locale-primary-input").val() || "").trim();
    } else if (this.state.isRichText && this.state.editorId) {
      const editor = tinymce.get(this.state.editorId);
      if (editor) {
        currentValue = (editor.getContent({ format: "text" }) || "").trim();
      }
    } else if (this.state.column === "product_slug" || this.state.column === "article_slug") {
      currentValue = ($('input[name="slug"]').val() || "").trim();
    }

    if (currentValue) {
      $("#aiModalPrompt").val(currentValue);
      this.refreshFinalPrompt();
      return;
    }

    // Try to read product title from the basic tab input
    const $titleInput = $('input[name$="[title]"]').first();
    if ($titleInput.length && $titleInput.val()) {
      $("#aiModalPrompt").val($titleInput.val().trim());
      this.refreshFinalPrompt();
    }
  },

  setSimpleModeDefault(tpl) {
    this._simpleTemplate = tpl;
  },

  /**
   * Get the final prompt based on current mode and input.
   */
  getFinalPrompt() {
    const mode = $(".ai-modal-mode-btn.active").data("mode");
    const userInput = ($("#aiModalPrompt").val() || "").trim();
    if (mode === "simple") {
      return (this._simpleTemplate || "") + userInput;
    }
    return userInput;
  },

  refreshFinalPrompt() {
    const final = this.getFinalPrompt();
    $("#aiModalFinalText").text(final);
  },

  /**
   * Collect all available locales from the dropdown.
   */
  getAllLocales() {
    const list = [];
    $("#aiModalLocale option").each(function () {
      const v = $(this).val();
      if (v) list.push(v);
    });
    return list;
  },

  async generate() {
    const prompt = this.getFinalPrompt();
    if (!prompt.trim()) {
      window.inno?.alert?.({ msg: "Please enter a prompt.", type: "warning" });
      return;
    }

    const targetLocale = $("#aiModalLocale").val();
    let locales;
    if (!this.state.isMultilingual) {
      // Single-language field (e.g. slug): generate once in English regardless of selector
      locales = ["en"];
    } else {
      locales = targetLocale ? [targetLocale] : this.getAllLocales();
    }
    if (locales.length === 0) {
      window.inno?.alert?.({ msg: "No locales available.", type: "warning" });
      return;
    }

    const $btn = $("#aiModalGenerateBtn");
    const originalHtml = $btn.html();
    $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-1"></span>');

    try {
      const res = await axios.post("{{ panel_route('content_ai.generate_batch') }}", {
        column: this.state.column,
        prompt: prompt,
        locales: locales,
        entity_type: this.state.entityType,
        entity_id: this.state.entityId,
      });

      const results = res?.data?.results || {};

      // Collect any per-locale errors so we can surface them to the user
      const errorMessages = [];
      Object.keys(results).forEach((locale) => {
        const r = results[locale] || {};
        if (r.error) {
          errorMessages.push(`[${locale}] ${r.error}`);
        }
      });
      if (errorMessages.length > 0) {
        window.inno?.alert?.({
          msg: errorMessages.join("\n"),
          type: "danger",
        });
      }

      // Helper: each locale result is now {text, error}
      const getText = (locale) => (results[locale] || {}).text || "";

      if (!this.state.isMultilingual) {
        // Single-language field (e.g. slug): take the first (only) result
        const firstLocale = Object.keys(results)[0];
        $("#aiModalResult").val(getText(firstLocale)).show();
        $("#aiModalResultMulti").empty().hide();
      } else if (targetLocale) {
        $("#aiModalResult").val(getText(targetLocale)).show();
        $("#aiModalResultMulti").empty().hide();
      } else {
        $("#aiModalResult").hide();
        const $multi = $("#aiModalResultMulti").empty().show();
        const labels = {};
        $("#aiModalLocale option").each(function () {
          labels[$(this).val()] = $(this).text();
        });
        Object.keys(results).forEach((locale) => {
          const label    = labels[locale] || locale;
          const r        = results[locale] || {};
          const errorFmt = r.error ? `<div class="text-danger small mb-1">${r.error}</div>` : "";
          $multi.append(
            '<div class="mb-2">' +
              '<label class="form-label small fw-medium mb-1">' + label + '</label>' +
              errorFmt +
              '<textarea class="form-control ai-result-locale" rows="4" data-locale="' + locale + '">' +
              (r.text || "") +
              '</textarea>' +
            '</div>'
          );
        });
      }
    } catch (err) {
      window.inno?.alert?.({
        msg: err.response?.data?.message || err.message || "AI generation failed.",
        type: "danger",
      });
    } finally {
      $btn.prop("disabled", false).html(originalHtml);
    }
  },

  apply() {
    // Single-language fields (e.g. slug): one result, one write-back
    if (!this.state.isMultilingual) {
      this.writeBack("", $("#aiModalResult").val());
      bootstrap.Modal.getInstance($("#aiGenerateModal")[0])?.hide();
      return;
    }

    const targetLocale = $("#aiModalLocale").val();

    if (targetLocale) {
      this.writeBack(targetLocale, $("#aiModalResult").val());
    } else {
      const self = this;
      $(".ai-result-locale").each(function () {
        self.writeBack($(this).data("locale"), $(this).val());
      });
    }

    bootstrap.Modal.getInstance($("#aiGenerateModal")[0])?.hide();
  },

  /**
   * Write generated text back to the originating field.
   * Handles three cases: TinyMCE editor, translation input, slug input.
   */
  writeBack(locale, text) {
    if (text === "" || text == null) return;

    if (this.state.isRichText) {
      // Rich text editor — convention elID: content-<locale>
      const editorId = "content-" + locale;
      const editor = tinymce.get(editorId);
      if (editor) {
        editor.setContent(text);
        tinymce.triggerSave();
        return;
      }
      // Fallback: write to textarea directly
      $('textarea[name="translations[' + locale + '][content]"]').val(text);
      return;
    }

    if (this.state.field) {
      // Translation input
      const name = "translations[" + locale + "][" + this.state.field + "]";
      const $input = $('input[name="' + name + '"], textarea[name="' + name + '"]');
      if ($input.length) {
        $input.val(text).addClass("is-valid");
        setTimeout(() => $input.removeClass("is-valid"), 2000);
      }
      return;
    }

    // Single-language fields (slug)
    if (this.state.column === "product_slug" || this.state.column === "article_slug") {
      const $slug = $('input[name="slug"]');
      if ($slug.length) {
        $slug.val(text).addClass("is-valid");
        setTimeout(() => $slug.removeClass("is-valid"), 2000);
      }
    }
  },
};

window.aiModal = aiModal;
aiModal.init();
</script>
