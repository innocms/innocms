import './bootstrap';

import './bootstrap-validation';
import './alert';
import "./autocomplete";

import common from "./common";
import fileManager from "./panel-file-manager";

window.inno = common;
window.inno.fileManagerIframe = fileManager.init;

const base = document.querySelector('base').href;
const editor_language = document.querySelector('meta[name="editor_language"]')?.content || 'zh_cn';

$(function () {
  tinymceInit();

  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
  const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

  $(document).on('focus', '.date input, .datetime input, .time input', function(event) {
    if (!$(this).prop('id')) $(this).prop('id', Math.random().toString(36).substring(2));

    $(this).attr('autocomplete', 'off');

    laydate.render({
      elem: '#' + $(this).prop('id'),
      type: $(this).parent().hasClass('date') ? 'date' : ($(this).parent().hasClass('datetime') ? 'datetime' : 'time'),
      trigger: 'click',
      lang: $('html').prop('lang') == 'zh-cn' ? 'cn' : 'en'
    });
  });
})

const tinymceInit = () => {
  if (typeof tinymce == 'undefined') {
    return;
  }

  tinymce.init({
    selector: '.tinymce',
    language: editor_language,
    branding: false,
    height: 500,
    table_class_list: [
      {title: 'Default', value: ''},
      {title: 'Bootstrap Table', value: 'table table-bordered'}
    ],
    convert_urls: false,
    // document_base_url: 'ssssss',
    inline: false,
    relative_urls: false,
    plugins: "link lists fullscreen table hr wordcount image imagetools code",
    menubar: "",
    toolbar: "undo redo | toolbarImageButton | lineheight | bold italic underline strikethrough | forecolor backcolor | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | formatpainter removeformat | charmap emoticons | preview | template link anchor table toolbarImageUrlButton | fullscreen code",
    // contextmenu: "link image imagetools table",
    toolbar_items_size: 'small',
    image_caption: true,
    imagetools_toolbar: '',
    toolbar_mode: 'wrap',
    font_formats:
      "微软雅黑='Microsoft YaHei';黑体=黑体;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Georgia=georgia,palatino;Helvetica=helvetica;Times New Roman=times new roman,times;Verdana=verdana,geneva",
    fontsize_formats: "10px 12px 14px 18px 24px 36px 48px 56px 72px 96px",
    lineheight_formats: "1 1.1 1.2 1.3 1.4 1.5 1.7 2.4 3 4",
    setup: function(ed) {
      ed.ui.registry.addButton('toolbarImageButton',{
        icon: 'image',
        onAction:function() {
          window.inno.fileManagerIframe((file) => {
            let url = file.url || file.origin_url;
            ed.insertContent('<img src="' + url + '" class="img-fluid" />');
          }, {
            multiple: false,
            type: 'image'
          });
        }
      });

      ed.on('NodeChange', function(e) {
        let table = ed.dom.getParent(e.element, 'table');
        if (table) {
          ed.dom.addClass(table, 'table table-bordered');
        }
      });
    }
  });
}