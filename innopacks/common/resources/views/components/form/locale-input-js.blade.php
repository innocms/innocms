@pushOnce('footer')
<script>
$(function() {
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

  // Smart fill button: copy primary text to empty fields
  $(document).on('click', '.locale-smart-fill-btn', function() {
    var $btn = $(this);
    var $wrapper = $btn.closest('.locale-field-wrapper');
    var primaryLocale = $btn.data('primary-locale');
    var msgNoEmpty = $btn.data('msg-no-empty');
    var msgCopied = $btn.data('msg-copied');

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

    emptyRows.forEach(function(item) {
      item.$input.val(primaryText);
    });

    inno.alert({ msg: msgCopied.replace(':count', emptyRows.length), type: 'success' });
  });
});
</script>
@endPushOnce
