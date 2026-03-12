{{-- 
    TinyMCE Rich Text Editor - Reusable Blade Partial
    
    Cách sử dụng:
    1. @include('components._tinymce')
    2. <textarea id="myEditor"></textarea>
    3. <script> initTinyMCE('#myEditor'); </script>
    
    Hoặc với options tùy chỉnh:
    <script> initTinyMCE('#myEditor', { height: 400 }); </script>
--}}

<style>
/* TinyMCE in modal - ensure floating elements (dropdowns, color pickers, dialogs) appear above modals */
.tox-tinymce-aux,
.tox.tox-silver-sink,
.tox .tox-dialog-wrap,
.tox .tox-menu,
.tox .tox-collection__group { z-index: 99999 !important; }

</style>
<script src="/vendor/tinymce/tinymce.min.js"></script>

<script>
/**
 * Khởi tạo TinyMCE trên một textarea/div
 * @param {string} selector - CSS selector (VD: '#myEditor')
 * @param {object} customOptions - Tùy chọn bổ sung (ghi đè mặc định)
 * @returns {Promise} - Promise resolve với TinyMCE editor instance
 */
function initTinyMCE(selector, customOptions = {}) {
    // Destroy existing instance nếu có
    const existingEditor = tinymce.get(selector.replace('#', ''));
    if (existingEditor) {
        existingEditor.destroy();
    }

    const defaultOptions = {
        selector: selector,
        height: customOptions.height || 350,
        license_key: 'gpl',
        
        menubar: 'file edit view insert format tools table',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount',
            'autoresize', 'directionality'
        ],
        toolbar: [
            'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
            'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | table charmap | removeformat fullscreen code help'
        ],
        toolbar_mode: 'wrap',
        
        // Font options
        font_family_formats: 'Arial=arial,helvetica,sans-serif; Roboto=Roboto,sans-serif; Times New Roman=times new roman,times,serif; Courier New=courier new,courier,monospace; Georgia=georgia,serif; Verdana=verdana,geneva,sans-serif; Tahoma=tahoma,arial,helvetica,sans-serif',
        font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt 28pt 36pt 48pt 72pt',
        
        // Block formats
        block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; Preformatted=pre; Blockquote=blockquote',
        
        // Image upload to S3 via backend API
        images_upload_url: '/media/editor-upload',
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/media/editor-upload');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        progress(Math.round(e.loaded / e.total * 100));
                    }
                };

                xhr.onload = function() {
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('Upload thất bại: ' + xhr.status);
                        return;
                    }
                    const json = JSON.parse(xhr.responseText);
                    if (!json || !json.location) {
                        reject('Upload thất bại: Không nhận được URL ảnh');
                        return;
                    }
                    resolve(json.location);
                };

                xhr.onerror = function() {
                    reject('Upload thất bại do lỗi kết nối');
                };

                xhr.send(formData);
            });
        },
        
        // Paste & drag-drop images auto upload
        automatic_uploads: true,
        images_reuse_filename: true,
        paste_data_images: true,
        
        
        // Table defaults
        table_default_styles: { 'border-collapse': 'collapse', 'width': '100%' },
        table_default_attributes: { 'border': '1' },
        
        // Content styling
        content_style: `
            body { 
                font-family: 'Roboto', Arial, sans-serif; 
                font-size: 14px; 
                line-height: 1.7; 
                color: #334155;
                padding: 12px;
            }
            img { max-width: 100%; height: auto; border-radius: 8px; }
            table { border-collapse: collapse; width: 100%; }
            table td, table th { border: 1px solid #e2e8f0; padding: 8px 12px; }
            table th { background: #f1f5f9; font-weight: 600; }
            blockquote { border-left: 4px solid #6d28d9; margin: 16px 0; padding: 12px 20px; background: #f8f5ff; }
            pre { background: #1e293b; color: #e2e8f0; padding: 16px; border-radius: 8px; overflow-x: auto; }
            code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
            a { color: #6d28d9; }
        `,
        
        // Promotion false (no upgrade nag)
        promotion: false,
        branding: false,
        
        // Resize
        resize: true,
        autoresize_bottom_margin: 20,
        min_height: 250,
        max_height: 600,
        
        // Setup callback
        setup: function(editor) {
            editor.on('init', function() {
                // Editor ready
            });
        }
    };

    // Merge custom options
    const finalOptions = Object.assign({}, defaultOptions, customOptions);
    
    return tinymce.init(finalOptions);
}

/**
 * Lấy nội dung HTML từ TinyMCE editor
 * @param {string} selector - CSS selector (VD: '#myEditor')
 * @returns {string} - Nội dung HTML
 */
function getTinyMCEContent(selector) {
    const editor = tinymce.get(selector.replace('#', ''));
    return editor ? editor.getContent() : '';
}

/**
 * Set nội dung HTML cho TinyMCE editor
 * @param {string} selector - CSS selector (VD: '#myEditor')
 * @param {string} content - Nội dung HTML
 */
function setTinyMCEContent(selector, content) {
    const editor = tinymce.get(selector.replace('#', ''));
    if (editor) {
        editor.setContent(content || '');
    }
}

/**
 * Destroy TinyMCE editor instance
 * @param {string} selector - CSS selector (VD: '#myEditor')
 */
function destroyTinyMCE(selector) {
    const editor = tinymce.get(selector.replace('#', ''));
    if (editor) {
        editor.destroy();
    }
}
</script>
