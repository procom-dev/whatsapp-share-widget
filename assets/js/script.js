$(document).ready(function() {
    const form = $('#wsw-generator-form');
    const preview = $('#widget-preview');
    const codeDisplay = $('#generated-code');
    const emailCodeDisplay = $('#generated-email-code');
    const fetchButton = $('#fetch-metadata');
    const description = $('#description');
    const charCount = $('.char-count');
    const customFields = $('.custom-fields');
    const toggleButton = $('#toggle-custom-fields');
    const loadingOverlay = $('.loading-overlay');
    const codeContainer = $('.code-container');
    const expandCodeBtn = $('.expand-code');
    const copyHtmlBtn = $('#copy-html');
    const copyEmailHtmlBtn = $('#copy-email-html');
    const togglePreviewBtn = $('#toggle-preview-settings');
    const previewSettings = $('.preview-settings');
    
    // Toggle preview settings
    togglePreviewBtn.click(function(e) {
        e.preventDefault();
        previewSettings.toggleClass('hidden');
        $(this).find('svg').toggleClass('rotate-180');
    });
    
    // Text formatting functions
    function insertFormat(format) {
        const textarea = document.getElementById('description');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let formatChars = '';
        
        switch(format) {
            case 'bold':
                formatChars = '*';
                break;
            case 'italic':
                formatChars = '_';
                break;
            case 'underline':
                formatChars = '~';
                break;
            case 'strike':
                formatChars = '~';
                break;
        }
        
        const selectedText = text.substring(start, end);
        const newText = text.substring(0, start) + formatChars + selectedText + formatChars + text.substring(end);
        
        textarea.value = newText;
        textarea.focus();
        textarea.setSelectionRange(start + formatChars.length, end + formatChars.length);
        
        updatePreviewAndCode();
    }
    
    // Format buttons click handlers
    $('.format-btn').click(function() {
        const format = $(this).data('format');
        insertFormat(format);
    });
    
    // Code expansion
    expandCodeBtn.click(function() {
        $(this).closest('.code-container').toggleClass('collapsed');
        $(this).find('.show-text, .hide-text').toggle();
    });
    
    // Copy HTML buttons
    copyHtmlBtn.click(function() {
        const code = codeDisplay.text();
        navigator.clipboard.writeText(code).then(() => {
            const $this = $(this);
            $this.text('Copied!');
            setTimeout(() => {
                $this.text('Copy HTML Code');
            }, 2000);
        });
    });

    copyEmailHtmlBtn.click(function() {
        const code = emailCodeDisplay.text();
        navigator.clipboard.writeText(code).then(() => {
            const $this = $(this);
            $this.text('Copied!');
            setTimeout(() => {
                $this.text('Copy HTML Code for Email');
            }, 2000);
        });
    });
    
    // Toggle custom fields
    toggleButton.click(function(e) {
        e.preventDefault();
        customFields.toggleClass('hidden');
        $(this).find('svg').toggleClass('rotate-180');
        $(this).find('.show-text, .hide-text').toggle();
    });
    
    // Character counter for description
    description.on('input', function() {
        const length = $(this).val().length;
        charCount.text(`${length}/2048`);
        if (length > 2048) {
            $(this).val($(this).val().substring(0, 2048));
        }
    });
    
    // Fetch metadata
    fetchButton.click(function(e) {
        e.preventDefault();
        const url = $('#url').val();
        if (!url) {
            alert('Please enter a URL first');
            return;
        }
        
        loadingOverlay.addClass('active');
        fetchButton.prop('disabled', true).text('Fetching...');
        
        $.ajax({
            url: 'fetch-metadata.php',
            method: 'POST',
            data: { url: url },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#title').val(data.title || '');
                    $('#description').val(data.description || '');
                    $('#thumbnail').val(data.thumbnail || '');
                    $('#domain').val(data.domain || '');
                    
                    updatePreviewAndCode();
                    description.trigger('input');
                } else {
                    throw new Error(response.message || 'Failed to fetch metadata');
                }
            },
            error: function(xhr, status, error) {
                console.error('Fetch Error:', error);
                alert(error);
            },
            complete: function() {
                fetchButton.prop('disabled', false).text('Fetch data');
                loadingOverlay.removeClass('active');
            }
        });
    });
    
    // Update preview and code on form changes
    $('#wsw-generator-form').on('input change', 'input, textarea', function() {
        clearTimeout($.data(this, 'timer'));
        $.data(this, 'timer', setTimeout(updatePreviewAndCode, 300));
    });
    
    function convertWhatsAppToHtml(text) {
        return text
            .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
            .replace(/_(.*?)_/g, '<em>$1</em>')
            .replace(/~(.*?)~/g, '<s>$1</s>');
    }
    
    function updatePreviewAndCode() {
        const data = {
            url: $('#url').val(),
            title: $('#title').val(),
            description: $('#description').val(),
            thumbnail: $('#thumbnail').val(),
            domain: $('#domain').val(),
            buttonText: $('#button-text').val() || 'Share on WhatsApp',
            redirectUrl: $('#redirect-url').val()
        };
        
        // For preview and HTML code, convert WhatsApp formatting to HTML
        const htmlDescription = convertWhatsAppToHtml(data.description);
        
        // Add URL to the share text (keep WhatsApp formatting for the share text)
        const shareText = `${data.description}\n\n${data.url}`;
        const encodedShareText = encodeURIComponent(shareText);
        
        const html = generateWidgetHtml(data, encodedShareText, htmlDescription);
        const emailHtml = generateEmailHtml(data, encodedShareText, htmlDescription);
        
        preview.html(html);
        codeDisplay.text(html);
        emailCodeDisplay.text(emailHtml);
    }
    
    function generateWidgetHtml(data, encodedShareText, htmlDescription) {
        const shareUrl = `https://wa.me/?text=${encodedShareText}`;
        const redirectScript = data.redirectUrl ? 
            `setTimeout(function() { window.location.href = '${data.redirectUrl}'; }, 500);` : '';
        
        return `<div style="display: flex; align-items: start; justify-content: center; padding: 20px 1rem;">
    <div style="max-width: 28rem; width: 100%; display: flex; flex-direction: column; align-items: center;">
        <div style="position: relative; margin-left: 0.5rem; margin-bottom: 1rem; width: 100%;">
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.31) !important; overflow: hidden; position: relative; margin-left: 0.5rem;">
                <div style="padding: 0.75rem;">
                    <p style="color: #111b21 !important; font-size: 14.2px !important; line-height: 19px !important; white-space: pre-line; margin: 0 !important; border: none !important;">${htmlDescription}</p>
                </div>
                <a href="${shareUrl}" target="_blank" onclick="${redirectScript}" style="text-decoration: none; color: inherit;">
                    <div style="border-top: 1px solid rgba(0, 0, 0, 0.0625); margin: 5px; border-radius: 0.5rem; overflow: hidden; background-color: #f4f5f6; cursor: pointer !important; transition: background-color 0.2s !important;">
                        <div style="width: 100%; height: 0; padding-bottom: 52.36%; position: relative; overflow: hidden;">
                            <img src="${data.thumbnail}" alt="Preview" style="position: absolute; top: 0; left: 0; width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 0.5rem 0.5rem 0 0 !important; display: block !important; margin: 0 !important; padding: 0 !important; border: none !important;">
                        </div>
                        <div style="padding: 0.75rem; background-color: #f4f5f6;">
                            <h3 style="all: unset !important; display: block !important; font-size: 14.2px !important; line-height: 19px !important; color: #111b21 !important; font-weight: normal !important; margin: 0 0 0.125rem 0 !important; padding: 0 !important; border: none !important;">${data.title}</h3>
                            <p style="font-size: 12.5px !important; line-height: 19px !important; color: #4b5563 !important; margin: 0 !important; padding: 0 !important; border: none !important; border-top: none !important;">${data.domain}</p>
                        </div>
                    </div>
                </a>
                <div style="position: absolute; left: -0.5rem; top: 0; width: 1rem; height: 1rem; overflow: hidden;">
                    <div style="position: absolute; width: 1rem; height: 1rem; background-color: white; transform: rotate(45deg); transform-origin: bottom right;"></div>
                </div>
            </div>
        </div>
        <div style="width: 100%; display: flex; justify-content: center; margin-top: 1rem; margin-bottom: 20px;">
            <a href="${shareUrl}" target="_blank" onclick="${redirectScript}" style="all: unset !important; width: 80% !important; background-color: #25D366 !important; color: white !important; font-weight: 600 !important; padding: 0.875rem 1rem !important; border-radius: 50px !important; border: none !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 0.5rem !important; transition: all 0.2s !important; position: relative !important; text-align: center !important; text-decoration: none !important;">
                ${data.buttonText}
            </a>
        </div>
    </div>
</div>`;
    }

    function generateEmailHtml(data, encodedShareText, htmlDescription) {
        const shareUrl = `https://wa.me/?text=${encodedShareText}`;
        
        return `<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 500px; margin: 0 auto;">
    <tr>
        <td style="padding: 0;">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: white; border-radius: 8px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <tr>
                    <td style="padding: 0;">
                        <a href="${shareUrl}" style="display: block; text-decoration: none; color: inherit;">
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                <tr>
                                    <td style="padding: 0;">
                                        <a href="${shareUrl}" style="display: block;">
                                            <img src="${data.thumbnail}" alt="Preview" style="width: 100%; height: auto; border-radius: 8px 8px 0 0; display: block;">
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 16px; background-color: #f4f5f6; border-radius: 0 0 8px 8px;">
                                        <h3 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px !important; line-height: 19px !important; color: #111b21 !important; font-weight: normal !important; margin: 0 0 4px 0 !important;">${data.title}</h3>
                                        <a href="${shareUrl}" style="text-decoration: none; color: inherit;">
                                            <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 10px !important; line-height: 19px !important; color: #4b5563 !important; margin: 0 !important;">${data.domain}</p>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding: 12px 0;">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                <tr>
                    <td align="center">
                        <a href="${shareUrl}" style="display: inline-block; background-color: #25D366; color: white; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; text-decoration: none; padding: 10px 20px; border-radius: 50px; text-align: center;">
                            ${data.buttonText}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<style>
@media screen and (max-width: 500px) {
    table {
        width: 100% !important;
    }
    td {
        font-size: 14px !important;
    }
    h3 {
        font-size: 12px !important;
    }
    p {
        font-size: 10px !important;
    }
    a {
        font-size: 12px !important;
        padding: 8px 16px !important;
    }
}
</style>`;
    }
    
    // Initialize with empty preview
    updatePreviewAndCode();
});