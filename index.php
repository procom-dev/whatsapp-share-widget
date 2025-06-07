<?php
session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://code.jquery.com https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data: https:; connect-src 'self'; font-src 'self' https://cdn.jsdelivr.net; frame-ancestors 'none';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Share Widget Generator</title>
    <link rel="icon" type="image/png" href="assets/img/favico.png">
    <link rel="shortcut icon" href="assets/img/favico.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-icons@0.344.0/font/lucide.min.css">
</head>
<body>
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <header class="site-header">
        <div class="header-container">
            <img src="assets/img/whatsapp-logo.png" alt="WhatsApp" class="header-logo">
            <h1 class="header-title">WhatsApp Share Widget Generator</h1>
        </div>
    </header>

    <main class="wsw-container" role="main">
        <div class="wsw-form">
            <form id="wsw-generator-form" aria-label="WhatsApp Widget Generator">
                <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group main-url-group">
                    <label for="url">URL to Share</label>
                    <p class="field-description" id="url-help">Enter the webpage URL you want to share on WhatsApp. We'll automatically fetch its metadata.</p>
                    <div class="url-group">
                        <input type="url" id="url" name="url" value="https://actionnetwork.org/forms/save-the-trees" required placeholder="Enter the URL you want to share" aria-describedby="url-help">
                        <button type="button" id="fetch-metadata" class="button" aria-describedby="fetch-help">
                            <i data-lucide="download" aria-hidden="true"></i>
                            Fetch data
                        </button>
                    </div>
                    <div id="fetch-help" class="sr-only">Automatically extracts title, description, and image from the provided URL</div>
                </div>
                
                <div class="action-buttons">
                    <button type="button" id="copy-html" class="primary-btn" aria-describedby="copy-help">
                        <i data-lucide="copy" aria-hidden="true"></i>
                        Copy HTML Code
                    </button>
                    <button type="button" id="copy-email-html" class="primary-btn" aria-describedby="copy-email-help">
                        <i data-lucide="mail" aria-hidden="true"></i>
                        Copy HTML Code for Email
                    </button>
                    <button type="button" id="toggle-custom-fields" class="secondary-btn" aria-expanded="false" aria-controls="custom-fields">
                        <i data-lucide="settings" aria-hidden="true"></i>
                        <span class="show-text">Customize Widget</span>
                        <span class="hide-text" style="display: none;">Hide Options</span>
                    </button>
                    <div id="copy-help" class="sr-only">Copies the generated HTML code for web use to your clipboard</div>
                    <div id="copy-email-help" class="sr-only">Copies the email-compatible HTML code to your clipboard</div>
                </div>
                
                <div class="custom-fields hidden" id="custom-fields" aria-labelledby="toggle-custom-fields">
                    <div class="form-group message-text-group">
                        <label for="description">Message Text</label>
                        <p class="field-description" id="description-help">This is the message that will be shared via WhatsApp. The link will be automatically added at the end.</p>
                        <div class="text-formatting-toolbar">
                            <button type="button" class="format-btn" data-format="bold" title="Bold"><strong>B</strong></button>
                            <button type="button" class="format-btn" data-format="italic" title="Italic"><em>I</em></button>
                            <button type="button" class="format-btn" data-format="underline" title="Underline"><u>U</u></button>
                            <button type="button" class="format-btn" data-format="strike" title="Strikethrough"><s>S</s></button>
                        </div>
                        <textarea id="description" name="description" placeholder="Enter the message that will be shared on WhatsApp" maxlength="2048" style="height: 300px; font-family: inherit;" aria-describedby="description-help char-count" aria-label="Message text for WhatsApp sharing">🌳 I just signed this petition to protect our forests! 🌿 

Our forests are the lungs of our planet, home to countless species, and vital for our survival. Every signature counts in making a real difference! 🌍

Join me in taking action! ✊</textarea>
                        <small class="char-count" id="char-count" aria-live="polite">0/2048</small>
                    </div>

                    <div class="form-group">
                        <label for="button-text">Button Text</label>
                        <p class="field-description" id="button-text-help">Customize the text that appears on the share button.</p>
                        <input type="text" id="button-text" name="button-text" value="Share on WhatsApp" placeholder="Enter button text" aria-describedby="button-text-help">
                    </div>
                    
                    <div class="form-group">
                        <label for="redirect-url">Redirect URL (Optional)</label>
                        <p class="field-description" id="redirect-help">After clicking the share button, users will be redirected to this URL (Website version only, not applicable for email).</p>
                        <input type="url" id="redirect-url" name="redirect-url" placeholder="Enter the redirect URL" aria-describedby="redirect-help">
                    </div>

                    <button type="button" id="toggle-preview-settings" class="text-btn">
                        <i data-lucide="settings-2"></i>
                        Link Preview Settings
                    </button>
                    <div class="preview-settings hidden">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" id="title" name="title" value="Sign now to save our forests!" placeholder="Enter the title for the preview">
                        </div>
                        
                        <div class="form-group">
                            <label for="thumbnail">Thumbnail URL</label>
                            <input type="url" id="thumbnail" name="thumbnail" value="https://can2-prod.s3.amazonaws.com/share_options/facebook_images/000/517/998/original/save-the-trees.png" placeholder="Enter the URL of the preview image">
                        </div>
                        
                        <div class="form-group">
                            <label for="domain">Domain</label>
                            <input type="text" id="domain" name="domain" value="actionnetwork.org" placeholder="Enter the domain name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="button-placement">Button Placement</label>
                        <p class="field-description">Choose where to display the share button in relation to the preview.</p>
                        <select id="button-placement" name="button-placement">
                            <option value="top-bottom" selected>Top & Bottom (Recommended)</option>
                            <option value="top">Top</option>
                            <option value="bottom">Bottom</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="add-tracking">Analytics Tracking</label>
                        <p class="field-description">Add source parameters to the final URL shared on WhatsApp, so you can track visits and events thanks to this widget (Recommended)</p>
                        <label class="checkbox-container">
                            <input type="checkbox" id="add-tracking" name="add-tracking" checked>
                            <span class="checkmark"></span>
                            Enable tracking parameters
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <section class="wsw-preview" aria-labelledby="preview-heading">
            <h2 id="preview-heading">Preview</h2>
            <div id="widget-preview" aria-live="polite" aria-label="Widget preview"></div>
        </section>

        <section class="wsw-code" aria-labelledby="code-heading">
            <h2 id="code-heading">Generated Code</h2>
            <div class="code-container collapsed">
                <pre><code id="generated-code" aria-label="Generated HTML code for web use"></code></pre>
                <div class="code-fade"></div>
                <div class="code-actions">
                    <button class="code-btn expand-code" aria-expanded="false" aria-controls="generated-code">
                        <i data-lucide="chevron-down" aria-hidden="true"></i>
                        <span class="show-text">Show full code</span>
                        <span class="hide-text" style="display: none;">Hide code</span>
                    </button>
                </div>
            </div>
        </section>

        <section class="wsw-code" aria-labelledby="email-code-heading">
            <h2 id="email-code-heading">Generated HTML Code for Email</h2>
            <div class="code-container collapsed">
                <pre><code id="generated-email-code" aria-label="Generated HTML code for email use"></code></pre>
                <div class="code-fade"></div>
                <div class="code-actions">
                    <button class="code-btn expand-code" aria-expanded="false" aria-controls="generated-email-code">
                        <i data-lucide="chevron-down" aria-hidden="true"></i>
                        <span class="show-text">Show full code</span>
                        <span class="hide-text" style="display: none;">Hide code</span>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Check if jQuery loaded successfully
        if (!window.jQuery) {
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"><\/script>');
        }
    </script>
    <script>
        // Fallback check for jQuery
        if (!window.jQuery) {
            console.warn('jQuery failed to load from CDN. Some features may not work properly.');
            // Create a minimal jQuery-like object for basic functionality
            window.$ = window.jQuery = function() {
                console.warn('jQuery not available. Please refresh the page or check your internet connection.');
                return {
                    ready: function(fn) { if (document.readyState === 'complete') fn(); else window.addEventListener('load', fn); },
                    click: function() { return this; },
                    on: function() { return this; },
                    val: function() { return ''; },
                    text: function() { return ''; },
                    find: function() { return this; },
                    addClass: function() { return this; },
                    removeClass: function() { return this; },
                    toggleClass: function() { return this; },
                    attr: function() { return this; },
                    prop: function() { return this; },
                    html: function() { return this; }
                };
            };
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Check if Lucide loaded successfully
        if (typeof lucide === 'undefined') {
            console.warn('Lucide icons failed to load from CDN. Icons may not display properly.');
            // Create minimal lucide fallback
            window.lucide = {
                createIcons: function() {
                    console.warn('Lucide icons not available. Icons will not be displayed.');
                }
            };
        }
    </script>
    <script src="assets/js/script.js"></script>
    <script>
        try {
            lucide.createIcons();
        } catch (e) {
            console.warn('Failed to initialize Lucide icons:', e);
        }
    </script>
</body>
</html>