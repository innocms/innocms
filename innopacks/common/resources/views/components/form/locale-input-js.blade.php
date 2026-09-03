@pushOnce('footer')
<script>
$(function() {
  // Remove name attributes from modal primary-locale inputs so they are not
  // submitted alongside the main input (which would create duplicate keys
  // and cause PHP to build an array instead of a string value).
  $('.locale-field-wrapper').each(function() {
    var $wrapper = $(this);
    var primaryLocale = $wrapper.data('panel-locale');
    var $modalPrimaryRow = $wrapper.find('.locale-modal-row[data-locale="' + primaryLocale + '"]');
    $modalPrimaryRow.find('input, textarea').removeAttr('name');
  });

  // Sync main input ↔ modal on show/close
  $(document).on('show.bs.modal', '.locale-field-wrapper .modal', function() {
    var $modal = $(this);
    var $wrapper = $modal.closest('.locale-field-wrapper');
    var primaryLocale = $wrapper.data('panel-locale');
    var mainValue = $wrapper.find('> .input-group .locale-primary-input').val() || '';
    var $modalPrimaryRow = $modal.find('.locale-modal-row[data-locale="' + primaryLocale + '"]');
    $modalPrimaryRow.find('input.form-control, textarea.form-control').val(mainValue);
  });

  $(document).on('hidden.bs.modal', '.locale-field-wrapper .modal', function() {
    var $modal = $(this);
    var $wrapper = $modal.closest('.locale-field-wrapper');
    var primaryLocale = $wrapper.data('panel-locale');
    var $modalPrimaryRow = $modal.find('.locale-modal-row[data-locale="' + primaryLocale + '"]');
    var modalValue = $modalPrimaryRow.find('input.form-control, textarea.form-control').val() || '';
    $wrapper.find('> .input-group .locale-primary-input').val(modalValue);
  });

  // Smart fill button: translate via API when a translator plugin is enabled,
  // otherwise fall back to copying the primary text into empty fields.
  $(document).on('click', '.locale-smart-fill-btn', function() {
    var $btn = $(this);
    var $wrapper = $btn.closest('.locale-field-wrapper');
    var primaryLocale = $btn.data('primary-locale');
    var hasTranslator = String($btn.data('has-translator')) === '1';
    var msgNoEmpty = $btn.data('msg-no-empty');
    var msgCopied = $btn.data('msg-copied');
    var msgTranslated = $btn.data('msg-translated');
    var msgFailed = $btn.data('msg-failed');

    var $modalPrimaryRow = $wrapper.find('.locale-modal-row[data-locale="' + primaryLocale + '"]');
    var primaryText = $modalPrimaryRow.find('input.form-control, textarea.form-control').val() || '';

    if (!primaryText.trim()) return;

    var emptyRows = [];
    $wrapper.find('.locale-modal-row').each(function() {
      var $row = $(this);
      var locale = $row.data('locale');
      if (locale === primaryLocale) return;
      var $input = $row.find('input.form-control, textarea.form-control');
      if (!$input.val() || !$input.val().trim()) {
        emptyRows.push({ $input: $input, locale: locale });
      }
    });

    if (emptyRows.length === 0) {
      inno.alert({ msg: msgNoEmpty, type: 'info' });
      return;
    }

    if (!hasTranslator) {
      emptyRows.forEach(function(item) {
        item.$input.val(primaryText);
      });

      inno.alert({ msg: msgCopied.replace(':count', emptyRows.length), type: 'success' });
      return;
    }

    // One request per target locale: TranslateRequest only accepts a string target.
    var originalHtml = $btn.html();
    var btnLabel = $btn.text().trim();
    var okCount = 0;
    var failCount = 0;
    var done = 0;
    var total = emptyRows.length;
    var firstError = '';

    $btn.prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>' + btnLabel);

    emptyRows.forEach(function(item) {
      axios.post('{{ panel_route('translations.trans_text') }}', {
        source: primaryLocale,
        target: item.locale,
        text: primaryText,
      }).then(function(data) {
        // Two failure layers: request-level (no translator) and per-locale error.
        if (!data || data.success === false) {
          failCount++;
          firstError = firstError || (data && data.message) || '';
          return;
        }

        var row = (data.data || [])[0];
        if (row && row.result && !row.error) {
          item.$input.val(row.result);
          okCount++;
        } else {
          // Leave the field empty so a failed translation is never mistaken
          // for a real one and silently saved.
          failCount++;
          firstError = firstError || (row && row.error) || '';
        }
      }).catch(function(err) {
        failCount++;
        firstError = firstError || (err && err.response && err.response.data && err.response.data.message) || (err && err.message) || '';
      }).finally(function() {
        done++;
        if (done < total) return;

        $btn.prop('disabled', false).html(originalHtml);

        // Single combined alert (same one-popup pattern as enterprise): success
        // and failure counts share one message, shown red when anything failed.
        var parts = [];
        if (okCount > 0) {
          parts.push(msgTranslated.replace(':count', okCount));
        }
        if (failCount > 0) {
          var failMsg = msgFailed.replace(':count', failCount);
          if (firstError) {
            failMsg += ': ' + firstError;
          }
          parts.push(failMsg);
          console.error('[locale-input] translation failed:', firstError);
        }

        if (parts.length > 0) {
          inno.alert({ msg: parts.join('; '), type: failCount > 0 ? 'danger' : 'success' });
        }
      });
    });
  });
});
</script>
@endPushOnce
