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
    
    // Error handling system
    const ErrorHandler = {
        messages: {
            'network_error': 'Unable to connect. Please check your internet connection and try again.',
            'invalid_url': 'Please enter a valid URL starting with http:// or https://',
            'fetch_failed': 'Unable to load website data. The website may be blocking requests or temporarily unavailable.',
            'timeout': 'Request timed out. The website is taking too long to respond.',
            'rate_limit': 'Please wait a moment before making another request.',
            'csrf_error': 'Security token expired. Please refresh the page and try again.',
            'forbidden': 'Unable to access this URL. It may be a private or restricted resource.',
            'server_error': 'Server error occurred. Please try again in a few moments.',
            'default': 'Something went wrong. Please try again.'
        },
        
        show: function(error, context = '') {
            const message = this.getErrorMessage(error);
            this.displayToUser(message);
            this.logForDebugging(error, context);
        },
        
        getErrorMessage: function(error) {
            if (typeof error === 'string') {
                return this.messages[error] || this.messages.default;
            }
            
            // Handle custom validation messages
            if (typeof error === 'object' && error.message) {
                return error.message;
            }
            
            if (error.status === 403) return this.messages.csrf_error;
            if (error.status === 429) return this.messages.rate_limit;
            if (error.status === 400) return this.messages.invalid_url;
            if (error.status >= 500) return this.messages.server_error;
            if (error.status === 0) return this.messages.network_error;
            
            return error.responseJSON?.message || this.messages.default;
        },
        
        displayToUser: function(message) {
            // Remove existing notifications
            $('.error-notification').remove();
            
            // Create and show notification
            const notification = $(`
                <div class="error-notification" role="alert" aria-live="assertive">
                    <div class="notification-content">
                        <span class="notification-message">${message}</span>
                        <button class="notification-close" aria-label="Close notification">&times;</button>
                    </div>
                </div>
            `);
            
            $('body').prepend(notification);
            
            // Auto-hide after 7 seconds
            setTimeout(() => {
                notification.fadeOut(300, () => notification.remove());
            }, 7000);
            
            // Close button functionality
            notification.find('.notification-close').click(() => {
                notification.fadeOut(300, () => notification.remove());
            });
        },
        
        logForDebugging: function(error, context) {
            console.group('Error Details');
            console.error('Context:', context);
            console.error('Error:', error);
            console.groupEnd();
        }
    };
    
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
                formatChars = '__';
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
        const container = $(this).closest('.code-container');
        const isCollapsed = container.hasClass('collapsed');
        container.toggleClass('collapsed');
        $(this).find('.show-text, .hide-text').toggle();
        
        // Update ARIA attributes
        $(this).attr('aria-expanded', isCollapsed);
    });
    
    // Consolidated copy to clipboard function
    function copyToClipboard(text, button, originalText) {
        const $button = $(button);
        
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                $button.text('Copied!');
                setTimeout(() => {
                    $button.text(originalText);
                }, 2000);
            }).catch(() => {
                ErrorHandler.show('default', 'Clipboard write failed');
            });
        } else {
            // Fallback for older browsers
            try {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                $button.text('Copied!');
                setTimeout(() => {
                    $button.text(originalText);
                }, 2000);
            } catch (err) {
                ErrorHandler.show('default', 'Copy fallback failed');
            }
        }
    }
    
    // Copy HTML buttons
    copyHtmlBtn.click(function() {
        copyToClipboard(codeDisplay.text(), this, 'Copy HTML Code');
    });

    copyEmailHtmlBtn.click(function() {
        copyToClipboard(emailCodeDisplay.text(), this, 'Copy HTML Code for Email');
    });
    
    // Toggle custom fields
    toggleButton.click(function(e) {
        e.preventDefault();
        const wasHidden = customFields.hasClass('hidden');
        customFields.toggleClass('hidden');
        $(this).find('svg').toggleClass('rotate-180');
        $(this).find('.show-text, .hide-text').toggle();
        
        // Update ARIA attributes
        $(this).attr('aria-expanded', !wasHidden);
        
        // Focus management
        if (!wasHidden) {
            // Focus first focusable element when expanding
            customFields.find('input, textarea, select, button').first().focus();
        }
    });
    
    // Keyboard navigation for format buttons
    $('.format-btn').on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });
    
    // Keyboard navigation for expand code buttons
    expandCodeBtn.on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).click();
        }
    });
    
    // Character counter for description with validation
    description.on('input', function() {
        const length = $(this).val().length;
        charCount.text(`${length}/2048`);
        
        // Visual feedback for character limit
        if (length > 2000) {
            charCount.css('color', '#dc3545'); // Red warning
        } else if (length > 1800) {
            charCount.css('color', '#fd7e14'); // Orange warning  
        } else {
            charCount.css('color', '#6c757d'); // Normal gray
        }
        
        if (length > 2048) {
            $(this).val($(this).val().substring(0, 2048));
            charCount.text('2048/2048');
            charCount.css('color', '#dc3545');
        }
    });
    
    // URL validation function
    function validateUrl(url) {
        if (!url || url.trim() === '') {
            return { valid: false, message: 'URL is required' };
        }
        
        url = url.trim();
        
        if (url.length > 2048) {
            return { valid: false, message: 'URL too long. Maximum 2048 characters allowed.' };
        }
        
        // Check if URL starts with http or https
        if (!url.match(/^https?:\/\//i)) {
            return { valid: false, message: 'URL must start with http:// or https://' };
        }
        
        // Basic URL format validation
        try {
            new URL(url);
        } catch (e) {
            return { valid: false, message: 'Please enter a valid URL' };
        }
        
        return { valid: true };
    }
    
    // Fetch metadata
    fetchButton.click(function(e) {
        e.preventDefault();
        const url = $('#url').val();
        const validation = validateUrl(url);
        
        if (!validation.valid) {
            ErrorHandler.show({ message: validation.message }, 'URL validation');
            return;
        }
        
        loadingOverlay.addClass('active');
        fetchButton.prop('disabled', true).text('Fetching...');
        
        $.ajax({
            url: 'fetch-metadata.php',
            method: 'POST',
            data: { 
                url: url,
                csrf_token: $('#csrf_token').val()
            },
            dataType: 'json',
            timeout: 10000, // 10 second timeout
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#title').val(data.title || '');
                    $('#description').val(data.description || '');
                    $('#thumbnail').val(data.thumbnail || '');
                    $('#domain').val(data.domain || '');
                    
                    // Set button text based on detected language
                    if (data.language) {
                        const translatedButtonText = getButtonTextForLanguage(data.language);
                        $('#button-text').val(translatedButtonText);
                    }
                    
                    updatePreviewAndCode();
                    description.trigger('input');
                } else {
                    ErrorHandler.show({responseJSON: response}, 'Metadata fetch response');
                }
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    ErrorHandler.show('timeout', 'AJAX timeout');
                } else if (status === 'abort') {
                    ErrorHandler.show('default', 'Request was cancelled');
                } else if (xhr.status === 0) {
                    ErrorHandler.show('network_error', 'Network connectivity');
                } else if (xhr.status === 408) {
                    ErrorHandler.show('timeout', 'Server timeout');
                } else {
                    ErrorHandler.show(xhr, 'AJAX error');
                }
            },
            complete: function() {
                fetchButton.prop('disabled', false).text('Fetch data');
                loadingOverlay.removeClass('active');
            }
        });
    });
    
    // Update preview and code on form changes
    $('#wsw-generator-form').on('input change', 'input, textarea, select', function() {
        clearTimeout($.data(this, 'timer'));
        $.data(this, 'timer', setTimeout(updatePreviewAndCode, 300));
    });
    
    function convertWhatsAppToHtml(text) {
        return text
            .replace(/\*(.*?)\*/g, '<strong>$1</strong>')
            .replace(/__(.*?)__/g, '<u>$1</u>')
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
            redirectUrl: $('#redirect-url').val(),
            addTracking: $('#add-tracking').is(':checked'),
            buttonPlacement: $('#button-placement').val() || 'top-bottom'
        };
        
        // For preview and HTML code, convert WhatsApp formatting to HTML
        const htmlDescription = convertWhatsAppToHtml(data.description);
        
        // Add tracking parameters to URL if enabled
        let finalUrl = data.url;
        if (data.addTracking && data.url) {
            const urlObj = new URL(data.url);
            urlObj.searchParams.set('source', 'whatsapp-share');
            urlObj.searchParams.set('utm_source', 'whatsapp-share');
            urlObj.searchParams.set('utm_medium', 'social');
            finalUrl = urlObj.toString();
        }
        
        // Add tracking parameters to redirect URL if enabled
        let finalRedirectUrl = data.redirectUrl;
        if (data.addTracking && data.redirectUrl) {
            const redirectUrlObj = new URL(data.redirectUrl);
            redirectUrlObj.searchParams.set('source', 'whatsapp-share');
            redirectUrlObj.searchParams.set('utm_source', 'whatsapp-share');
            finalRedirectUrl = redirectUrlObj.toString();
        }
        
        // Add URL to the share text (keep WhatsApp formatting for the share text)
        const shareText = `${data.description}\n\n${finalUrl}`;
        const encodedShareText = encodeURIComponent(shareText);
        
        const html = generateWidgetHtml(data, encodedShareText, htmlDescription, finalUrl, finalRedirectUrl);
        const emailHtml = generateEmailHtml(data, encodedShareText, htmlDescription, finalUrl);
        
        preview.html(html);
        codeDisplay.text(html);
        emailCodeDisplay.text(emailHtml);
    }
    
    function generateWidgetHtml(data, encodedShareText, htmlDescription, finalUrl, finalRedirectUrl) {
        const shareUrl = `https://wa.me/?text=${encodedShareText}`;
        const redirectScript = finalRedirectUrl ? 
            `setTimeout(function() { window.location.href = '${finalRedirectUrl}'; }, 500);` : '';
        
        // Create the WhatsApp button with PNG icon
        const whatsappButton = `<a href="${shareUrl}" target="_blank" onclick="${redirectScript}" style="all: unset !important; width: 80% !important; background-color: #25D366 !important; color: white !important; font-weight: 600 !important; padding: 0.875rem 1rem !important; border-radius: 50px !important; border: none !important; cursor: pointer !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 0.5rem !important; transition: all 0.2s !important; position: relative !important; text-decoration: none !important;">
            <img src="${window.location.origin}/assets/img/whatsapp-logo.png" alt="WhatsApp" style="width: 20px !important; height: 20px !important; margin-right: 0.5rem !important;">
            ${data.buttonText}
        </a>`;
        
        // Create the preview bubble with image error handling
        const imageHtml = data.thumbnail ? 
            `<img src="${data.thumbnail}" alt="Preview" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem 0.5rem 0 0; display: block; margin: 0; padding: 0; border: none;" onerror="this.style.display='none'; this.parentElement.style.paddingBottom='0'; this.parentElement.style.height='auto';">` : 
            `<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.8rem;">No image</div>`;
        
        const previewBubble = `<div style="position: relative; margin-left: 0.5rem; margin-bottom: 1rem; width: 100%;">
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.31); overflow: hidden; position: relative; margin-left: 0.5rem;">
                <div style="padding: 0.75rem;">
                    <p style="color: #111b21 !important; font-size: 14.2px !important; line-height: 19px !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important; font-weight: normal !important; margin: 0 0 0 0 !important; letter-spacing: 0px !important; white-space: pre-line; border: none;">${htmlDescription}</p>
                </div>
                <a href="${shareUrl}" target="_blank" onclick="${redirectScript}" style="text-decoration: none; color: inherit;">
                    <div style="border-top: 1px solid rgba(0, 0, 0, 0.0625); margin: 5px; border-radius: 0.5rem; overflow: hidden; background-color: #f4f5f6; cursor: pointer; transition: background-color 0.2s;">
                        <div style="width: 100%; height: 0; padding-bottom: ${data.thumbnail ? '52.36%' : '0'}; position: relative; overflow: hidden;">
                            ${imageHtml}
                        </div>
                        <div style="padding: 0.75rem; background-color: #f4f5f6;">
                            <h3 style="all: unset; display: block; font-size: 14.2px !important; line-height: 19px !important; color: #111b21 !important; font-weight: normal !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important; margin: 0 0 0.125rem 0 !important; letter-spacing: 0px !important; padding: 0; border: none;">${data.title}</h3>
                            <p style="font-size: 12.5px !important; line-height: 19px !important; color: #4b5563 !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important; font-weight: normal !important; margin: 0 0 0 0 !important; letter-spacing: 0px !important; padding: 0; border: none; border-top: none;">${data.domain}</p>
                        </div>
                    </div>
                </a>
                <div style="position: absolute; left: -0.5rem; top: 0; width: 1rem; height: 1rem; overflow: hidden;">
                    <div style="position: absolute; width: 1rem; height: 1rem; background-color: white; transform: rotate(45deg); transform-origin: bottom right;"></div>
                </div>
            </div>
        </div>`;
        
        // Handle button placement
        let html = `<div style="display: flex; align-items: start; justify-content: center; padding: 20px 1rem;">
    <div style="max-width: 28rem; width: 100%; display: flex; flex-direction: column; align-items: center;">`;
        
        if (data.buttonPlacement === 'top' || data.buttonPlacement === 'top-bottom') {
            html += `\n        <div style="width: 100%; display: flex; justify-content: center; margin-bottom: 2rem;">
            ${whatsappButton}
        </div>`;
        }
        
        html += `\n        ${previewBubble}`;
        
        if (data.buttonPlacement === 'bottom' || data.buttonPlacement === 'top-bottom') {
            html += `\n        <div style="width: 100%; display: flex; justify-content: center; margin-top: 2rem; margin-bottom: 20px;">
            ${whatsappButton}
        </div>`;
        }
        
        html += `\n    </div>
</div>`;
        
        return html;
    }

    function generateEmailHtml(data, encodedShareText, htmlDescription, finalUrl) {
        // For email, use different tracking parameter
        let emailUrl = finalUrl;
        if (data.addTracking && data.url) {
            const urlObj = new URL(data.url);
            urlObj.searchParams.set('source', 'whatsapp-share-email');
            urlObj.searchParams.set('utm_source', 'whatsapp-share-email');
            urlObj.searchParams.set('utm_medium', 'social');
            emailUrl = urlObj.toString();
            
            // Update share text for email
            const emailShareText = `${data.description}\n\n${emailUrl}`;
            const emailEncodedShareText = encodeURIComponent(emailShareText);
            var shareUrl = `https://wa.me/?text=${emailEncodedShareText}`;
        } else {
            var shareUrl = `https://wa.me/?text=${encodedShareText}`;
        }
        
        // Create the WhatsApp button for email
        const whatsappButton = `<a href="${shareUrl}" style="display: inline-block; background-color: #25D366; color: white; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 600; text-decoration: none; padding: 10px 20px; border-radius: 50px; text-align: center;">
            <img src="${window.location.origin}/assets/img/whatsapp-logo.png" alt="WhatsApp" style="width: 16px; height: 16px; margin-right: 8px; vertical-align: middle;" onerror="this.style.display='none'; this.style.marginRight='0';">
            ${data.buttonText}
        </a>`;
        
        // Create the preview table with image error handling for email
        const emailImageHtml = data.thumbnail ? 
            `<tr>
                <td style="padding: 0;">
                    <a href="${shareUrl}" style="display: block;">
                        <img src="${data.thumbnail}" alt="Preview" style="width: 100%; height: auto; border-radius: 8px 8px 0 0; display: block;" onerror="this.parentElement.parentElement.parentElement.style.display='none';">
                    </a>
                </td>
            </tr>` : '';
        
        const previewTable = `<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: white; border-radius: 8px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                <tr>
                    <td style="padding: 0;">
                        <a href="${shareUrl}" style="display: block; text-decoration: none; color: inherit;">
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                ${emailImageHtml}
                                <tr>
                                    <td style="padding: 16px; background-color: #f4f5f6; border-radius: ${data.thumbnail ? '0 0 8px 8px' : '8px'};">
                                        <h3 style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important; font-size: 14px !important; line-height: 19px !important; color: #111b21 !important; font-weight: normal !important; margin: 0 0 4px 0 !important; letter-spacing: 0px !important;">${data.title}</h3>
                                        <a href="${shareUrl}" style="text-decoration: none; color: inherit;">
                                            <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !important; font-size: 10px !important; line-height: 19px !important; color: #4b5563 !important; font-weight: normal !important; margin: 0 0 0 0 !important; letter-spacing: 0px !important;">${data.domain}</p>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </a>
                    </td>
                </tr>
            </table>`;
        
        // Handle button placement for email
        let html = `<table cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 500px; margin: 0 auto;">`;
        
        if (data.buttonPlacement === 'top' || data.buttonPlacement === 'top-bottom') {
            html += `\n    <tr>
        <td style="padding: 0 0 24px 0;">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                <tr>
                    <td align="center">
                        ${whatsappButton}
                    </td>
                </tr>
            </table>
        </td>
    </tr>`;
        }
        
        html += `\n    <tr>
        <td style="padding: 0;">
            ${previewTable}
        </td>
    </tr>`;
        
        if (data.buttonPlacement === 'bottom' || data.buttonPlacement === 'top-bottom') {
            html += `\n    <tr>
        <td style="padding: 24px 0;">
            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                <tr>
                    <td align="center">
                        ${whatsappButton}
                    </td>
                </tr>
            </table>
        </td>
    </tr>`;
        }
        
        html += `\n</table>

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
        
        return html;
    }
    
    // Language translations for button text
    const buttonTranslations = {
        'en': 'Share on WhatsApp',
        'es': 'Compartir en WhatsApp',
        'ca': 'Comparteix per WhatsApp',
        'eu': 'WhatsApp-en partekatu',
        'gl': 'Compartir no WhatsApp',
        'fr': 'Partager sur WhatsApp',
        'de': 'Auf WhatsApp teilen',
        'it': 'Condividi su WhatsApp',
        'pt': 'Compartilhar no WhatsApp',
        'nl': 'Delen op WhatsApp',
        'ru': 'Поделиться в WhatsApp',
        'ar': 'شارك على واتساب',
        'zh': '在WhatsApp上分享',
        'ja': 'WhatsAppで共有',
        'ko': 'WhatsApp에서 공유',
        'hi': 'WhatsApp पर साझा करें',
        'tr': 'WhatsApp\'ta Paylaş',
        'pl': 'Udostępnij na WhatsApp',
        'sv': 'Dela på WhatsApp',
        'da': 'Del på WhatsApp',
        'no': 'Del på WhatsApp',
        'fi': 'Jaa WhatsAppissa',
        'he': 'שתף בוואטסאפ',
        'th': 'แชร์ใน WhatsApp',
        'vi': 'Chia sẻ trên WhatsApp',
        'id': 'Bagikan di WhatsApp',
        'ms': 'Kongsi di WhatsApp',
        'tl': 'Ibahagi sa WhatsApp',
        'uk': 'Поділитися в WhatsApp',
        'cs': 'Sdílet na WhatsApp',
        'sk': 'Zdieľať na WhatsApp',
        'hu': 'Megosztás WhatsApp-on',
        'ro': 'Distribuie pe WhatsApp',
        'bg': 'Споделяне в WhatsApp',
        'hr': 'Podijeli na WhatsApp',
        'sr': 'Подели на WhatsApp',
        'sl': 'Deli na WhatsApp',
        'lt': 'Dalintis WhatsApp',
        'lv': 'Dalīties WhatsApp',
        'et': 'Jaga WhatsAppis'
    };
    
    function getButtonTextForLanguage(language) {
        return buttonTranslations[language] || buttonTranslations['en'];
    }
    
    // Initialize with empty preview
    updatePreviewAndCode();
});