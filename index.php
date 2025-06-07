<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Share Widget Generator</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-icons@0.344.0/font/lucide.min.css">
</head>
<body>
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="wsw-container">
        <div class="wsw-form">
            <form id="wsw-generator-form">
                <div class="form-group main-url-group">
                    <label for="url">URL to Share</label>
                    <p class="field-description">Enter the webpage URL you want to share on WhatsApp. We'll automatically fetch its metadata.</p>
                    <div class="url-group">
                        <input type="url" id="url" name="url" value="https://actionnetwork.org/forms/save-the-trees?source=whatsapp" required placeholder="Enter the URL you want to share">
                        <button type="button" id="fetch-metadata" class="button">
                            <i data-lucide="download"></i>
                            Fetch data
                        </button>
                    </div>
                    <p class="field-description text-sm text-gray-500 mt-2">Recommended: add a tracking parameter to the URL like ?source=whatsapp</p>
                </div>
                
                <div class="action-buttons">
                    <button type="button" id="copy-html" class="primary-btn">
                        <i data-lucide="copy"></i>
                        Copy HTML Code
                    </button>
                    <button type="button" id="copy-email-html" class="primary-btn">
                        <i data-lucide="mail"></i>
                        Copy HTML Code for Email
                    </button>
                    <button type="button" id="toggle-custom-fields" class="secondary-btn">
                        <i data-lucide="settings"></i>
                        <span class="show-text">Customize Widget</span>
                        <span class="hide-text" style="display: none;">Hide Options</span>
                    </button>
                </div>
                
                <div class="custom-fields hidden">
                    <div class="form-group message-text-group">
                        <label for="description">Message Text</label>
                        <p class="field-description">This is the message that will be shared via WhatsApp. The link will be automatically added at the end.</p>
                        <div class="text-formatting-toolbar">
                            <button type="button" class="format-btn" data-format="bold" title="Bold"><strong>B</strong></button>
                            <button type="button" class="format-btn" data-format="italic" title="Italic"><em>I</em></button>
                            <button type="button" class="format-btn" data-format="underline" title="Underline"><u>U</u></button>
                            <button type="button" class="format-btn" data-format="strike" title="Strikethrough"><s>S</s></button>
                        </div>
                        <textarea id="description" name="description" placeholder="Enter the message that will be shared on WhatsApp" maxlength="2048" style="height: 300px; font-family: inherit;">🌳 I just signed this petition to protect our forests! 🌿 

Our forests are the lungs of our planet, home to countless species, and vital for our survival. Every signature counts in making a real difference! 🌍

Join me in taking action! ✊</textarea>
                        <small class="char-count">0/2048</small>
                    </div>

                    <div class="form-group">
                        <label for="button-text">Button Text</label>
                        <p class="field-description">Customize the text that appears on the share button.</p>
                        <input type="text" id="button-text" name="button-text" value="Share on WhatsApp" placeholder="Enter button text">
                    </div>
                    
                    <div class="form-group">
                        <label for="redirect-url">Redirect URL (Optional)</label>
                        <p class="field-description">After clicking the share button, users will be redirected to this URL (Website version only, not applicable for email).</p>
                        <input type="url" id="redirect-url" name="redirect-url" placeholder="Enter the redirect URL">
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
                </div>
            </form>
        </div>

        <div class="wsw-preview">
            <h2>Preview</h2>
            <div id="widget-preview"></div>
        </div>

        <div class="wsw-code">
            <h2>Generated Code</h2>
            <div class="code-container collapsed">
                <pre><code id="generated-code"></code></pre>
                <div class="code-fade"></div>
                <div class="code-actions">
                    <button class="code-btn expand-code">
                        <i data-lucide="chevron-down"></i>
                        <span class="show-text">Show full code</span>
                        <span class="hide-text" style="display: none;">Hide code</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="wsw-code">
            <h2>Generated HTML Code for Email</h2>
            <div class="code-container collapsed">
                <pre><code id="generated-email-code"></code></pre>
                <div class="code-fade"></div>
                <div class="code-actions">
                    <button class="code-btn expand-code">
                        <i data-lucide="chevron-down"></i>
                        <span class="show-text">Show full code</span>
                        <span class="hide-text" style="display: none;">Hide code</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="assets/js/script.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>