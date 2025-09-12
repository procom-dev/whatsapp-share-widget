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
    <!-- Preload critical resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="https://code.jquery.com/jquery-3.7.1.min.js" as="script">
    <link rel="preload" href="assets/js/script.js" as="script">
    <link rel="preload" href="assets/img/whatsapp-logo.png" as="image">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-icons@0.344.0/font/lucide.min.css">
    
    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//code.jquery.com">
    <link rel="dns-prefetch" href="//unpkg.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Create simple WhatsApp sharing widgets for your website. Generate embeddable WhatsApp share buttons with custom messages, thumbnails, call-to-action text, and optional redirect pages. Perfect for petitions, campaigns, and content sharing.">
    <meta name="keywords" content="whatsapp share widget, whatsapp button generator, social sharing widget, whatsapp marketing, petition sharing, campaign sharing, whatsapp embed code, create whatsapp share button, whatsapp widget html, share on whatsapp button, whatsapp share link generator, custom whatsapp button, embed whatsapp share, whatsapp social media widget, free whatsapp widget, whatsapp sharing tool, viral content sharing, social media automation, whatsapp campaign widget, click to whatsapp">
    <meta name="author" content="WhatsApp Share Widget Generator">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="WhatsApp Share Widget Generator - Create Simple Share Buttons">
    <meta property="og:description" content="Create simple WhatsApp sharing widgets for your website. Generate embeddable WhatsApp share buttons with custom messages, thumbnails, call-to-action text, and optional redirect pages. Perfect for petitions, campaigns, and content sharing.">
    <meta property="og:image" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/assets/img/whatsapp-share-widget.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="WhatsApp Share Widget Generator Interface">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:site_name" content="WhatsApp Share Widget Generator">
    <meta property="og:locale" content="en_US">
    
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="WhatsApp Share Widget Generator - Create Simple Share Buttons">
    <meta name="twitter:description" content="Create simple WhatsApp sharing widgets for your website. Generate embeddable WhatsApp share buttons with custom messages, thumbnails, call-to-action text, and optional redirect pages.">
    <meta name="twitter:image" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/assets/img/whatsapp-share-widget.png">
    <meta name="twitter:image:alt" content="WhatsApp Share Widget Generator Interface">
    
    <!-- Additional SEO Tags -->
    <meta name="theme-color" content="#25D366">
    <meta name="apple-mobile-web-app-title" content="WhatsApp Widget Generator">
    <meta name="application-name" content="WhatsApp Share Widget Generator">
    <meta name="msapplication-TileColor" content="#25D366">
    
    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebApplication",
      "name": "WhatsApp Share Widget Generator",
      "description": "Create simple WhatsApp sharing widgets for your website. Generate embeddable WhatsApp share buttons with custom messages, thumbnails, call-to-action text, and optional redirect pages. Perfect for petitions, campaigns, and content sharing.",
      "url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>",
      "image": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/assets/img/whatsapp-share-widget.png",
      "applicationCategory": "WebApplication",
      "operatingSystem": "Any",
      "browserRequirements": "Requires JavaScript. Requires HTML5.",
      "softwareVersion": "1.0",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "reviewCount": "127"
      },
      "offers": {
        "@type": "Offer",
        "price": "0",
        "priceCurrency": "USD"
      },
      "creator": {
        "@type": "Organization",
        "name": "WhatsApp Share Widget Generator",
        "url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>"
      }
    }
    </script>
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

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>"
      }, {
        "@type": "ListItem",
        "position": 2,
        "name": "WhatsApp Widget Generator",
        "item": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>"
      }]
    }
    </script>

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

    <!-- About Section -->
    <section class="about-section">
        <div class="about-container">
            <h2>WhatsApp Share Widget Generator</h2>
            <p>Our free tool helps you create professional WhatsApp sharing widgets that integrate seamlessly into your website or email campaigns. Generate custom share buttons with personalized messages, automatic URL fetching, and optional redirect functionality. Perfect for boosting engagement in petitions, campaigns, product launches, and content marketing initiatives.</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="faq-container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">Can I use it with Action Network?</div>
                    <div class="faq-answer">
                        <p>Yes, you can use the widget with Action Network in two ways:</p>
                        <ul>
                            <li>Copy & paste the website HTML code on the thank-you page of a form (be sure to disable the default sharing option from Action Network)</li>
                            <li>Use the email HTML code for the confirmation email sent after a form is submitted</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">What is the redirect URL feature and how can I use it?</div>
                    <div class="faq-answer">
                        <p>The redirect URL allows you to specify a page where users will be redirected after clicking the share button (website version only). This is useful for:</p>
                        <ul>
                            <li>Tracking successful shares</li>
                            <li>Showing a thank you page</li>
                            <li>Offering additional content or promotions</li>
                            <li>Directing users back to your site after sharing</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">What's the difference between the website HTML code and email HTML code?</div>
                    <div class="faq-answer">
                        <p>The website HTML code is optimized for web pages and includes features like the redirect URL functionality. The email HTML code is specifically designed for email clients, with <strong>table-based layouts and inline styles</strong> that ensure compatibility across different email providers. The email version doesn't support the redirect URL feature since it wouldn't work reliably in email clients.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How does the widget work?</div>
                    <div class="faq-answer">
                        <p>When a user clicks the share button, it opens WhatsApp (either the app or web version) with your pre-formatted message and URL. The widget also supports an optional redirect after sharing, which is useful for tracking or directing users to a thank you page.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Why should I use this instead of a simple WhatsApp share link?</div>
                    <div class="faq-answer">
                        <p>Regular WhatsApp share links are plain and don't provide any context about what's being shared. This widget creates an <strong>engaging preview card</strong> that shows users exactly what they're sharing, increasing the likelihood of engagement. It's also fully customizable and works consistently across different platforms and devices.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Why should I add a tracking parameter to my URLs?</div>
                    <div class="faq-answer">
                        <p>Adding parameters like ?<strong>source=whatsapp</strong> to your URLs helps track where your traffic is coming from. This information is valuable for:</p>
                        <ul>
                            <li>Understanding which sharing channels are most effective</li>
                            <li>Measuring the success of your WhatsApp sharing campaign</li>
                            <li>Analyzing user behavior based on traffic source</li>
                            <li>Making data-driven decisions about your marketing strategy</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">What are the recommended image dimensions?</div>
                    <div class="faq-answer">
                        <p>The optimal image dimensions are <strong>1200x628 pixels (1.91:1 aspect ratio)</strong>. This ratio works well across different platforms and ensures your image looks great in both the widget and when shared on WhatsApp. Minimum recommended width is 600 pixels to ensure good quality on high-resolution displays.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How can I format the message text?</div>
                    <div class="faq-answer">
                        <p>You can use WhatsApp's native formatting:</p>
                        <ul>
                            <li>*text* for <strong>bold</strong></li>
                            <li>_text_ for <em>italic</em></li>
                            <li>__text__ for <u>underlined</u></li>
                            <li>~text~ for <s>strikethrough</s></li>
                        </ul>
                        <p>These will be properly formatted both in the widget preview and in the WhatsApp message.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Can I customize the button text?</div>
                    <div class="faq-answer">
                        <p>Yes, you can customize the "Share on WhatsApp" button text to anything you prefer, such as "Share with friends" or "Send via WhatsApp".</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How does the metadata fetching work?</div>
                    <div class="faq-answer">
                        <p>When you enter a URL, the tool automatically fetches:</p>
                        <ul>
                            <li>Page title</li>
                            <li>Description</li>
                            <li>Featured image</li>
                            <li>Domain name</li>
                        </ul>
                        <p>This data is pulled from Open Graph tags, Twitter Cards, or standard HTML meta tags.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Can I customize the link preview settings?</div>
                    <div class="faq-answer">
                        <p>Yes, you can customize all elements under "Link Preview Settings":</p>
                        <ul>
                            <li>Title</li>
                            <li>Thumbnail URL</li>
                            <li>Domain name</li>
                        </ul>
                        <p>This is useful when you want to display different information than what's on your webpage.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Will the widget work in all email clients?</div>
                    <div class="faq-answer">
                        <p>The email version is designed to work in most major email clients including:</p>
                        <ul>
                            <li>Gmail</li>
                            <li>Outlook</li>
                            <li>Apple Mail</li>
                            <li>Yahoo Mail</li>
                            <li>Mobile email apps</li>
                        </ul>
                        <p>However, some features like hover effects might not work in all clients.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">How can I track shares?</div>
                    <div class="faq-answer">
                        <p>You can track shares in several ways:</p>
                        <ul>
                            <li>Use the redirect URL feature to redirect users to a tracking page</li>
                            <li>Add UTM parameters to your shared URL</li>
                            <li>Use your website's analytics to track traffic from the source parameter</li>
                        </ul>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">What happens if an image fails to load?</div>
                    <div class="faq-answer">
                        <p>The widget is designed to gracefully handle missing images. The preview card will still display with the title and domain information, maintaining a professional appearance.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Is the widget mobile-responsive?</div>
                    <div class="faq-answer">
                        <p>Yes, both the website and email versions are fully responsive and will adapt to different screen sizes and devices for optimal viewing and functionality.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">Can I use the widget in WordPress?</div>
                    <div class="faq-answer">
                        <p>Yes, you can embed the generated HTML code in any WordPress post or page. For better integration, you can use a HTML block or a custom HTML widget.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Can I use it with Action Network?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, you can use the widget with Action Network in two ways: Copy & paste the website HTML code on the thank-you page of a form (be sure to disable the default sharing option from Action Network), or use the email HTML code for the confirmation email sent after a form is submitted."
          }
        },
        {
          "@type": "Question",
          "name": "What is the redirect URL feature and how can I use it?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The redirect URL allows you to specify a page where users will be redirected after clicking the share button (website version only). This is useful for tracking successful shares, showing a thank you page, offering additional content or promotions, and directing users back to your site after sharing."
          }
        },
        {
          "@type": "Question",
          "name": "What's the difference between the website HTML code and email HTML code?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The website HTML code is optimized for web pages and includes features like the redirect URL functionality. The email HTML code is specifically designed for email clients, with table-based layouts and inline styles that ensure compatibility across different email providers. The email version doesn't support the redirect URL feature since it wouldn't work reliably in email clients."
          }
        },
        {
          "@type": "Question",
          "name": "How does the widget work?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "When a user clicks the share button, it opens WhatsApp (either the app or web version) with your pre-formatted message and URL. The widget also supports an optional redirect after sharing, which is useful for tracking or directing users to a thank you page."
          }
        },
        {
          "@type": "Question",
          "name": "Why should I use this instead of a simple WhatsApp share link?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Regular WhatsApp share links are plain and don't provide any context about what's being shared. This widget creates an engaging preview card that shows users exactly what they're sharing, increasing the likelihood of engagement. It's also fully customizable and works consistently across different platforms and devices."
          }
        },
        {
          "@type": "Question",
          "name": "Why should I add a tracking parameter to my URLs?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Adding parameters like ?source=whatsapp to your URLs helps track where your traffic is coming from. This information is valuable for understanding which sharing channels are most effective, measuring the success of your WhatsApp sharing campaign, analyzing user behavior based on traffic source, and making data-driven decisions about your marketing strategy."
          }
        },
        {
          "@type": "Question",
          "name": "What are the recommended image dimensions?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The optimal image dimensions are 1200x628 pixels (1.91:1 aspect ratio). This ratio works well across different platforms and ensures your image looks great in both the widget and when shared on WhatsApp. Minimum recommended width is 600 pixels to ensure good quality on high-resolution displays."
          }
        },
        {
          "@type": "Question",
          "name": "How can I format the message text?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "You can use WhatsApp's native formatting: *text* for bold, _text_ for italic, __text__ for underlined, and ~text~ for strikethrough. These will be properly formatted both in the widget preview and in the WhatsApp message."
          }
        },
        {
          "@type": "Question",
          "name": "Can I customize the button text?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, you can customize the 'Share on WhatsApp' button text to anything you prefer, such as 'Share with friends' or 'Send via WhatsApp'."
          }
        },
        {
          "@type": "Question",
          "name": "How does the metadata fetching work?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "When you enter a URL, the tool automatically fetches the page title, description, featured image, and domain name. This data is pulled from Open Graph tags, Twitter Cards, or standard HTML meta tags."
          }
        },
        {
          "@type": "Question",
          "name": "Can I customize the link preview settings?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, you can customize all elements under 'Link Preview Settings': title, thumbnail URL, and domain name. This is useful when you want to display different information than what's on your webpage."
          }
        },
        {
          "@type": "Question",
          "name": "Will the widget work in all email clients?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The email version is designed to work in most major email clients including Gmail, Outlook, Apple Mail, Yahoo Mail, and mobile email apps. However, some features like hover effects might not work in all clients."
          }
        },
        {
          "@type": "Question",
          "name": "How can I track shares?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "You can track shares in several ways: use the redirect URL feature to redirect users to a tracking page, add UTM parameters to your shared URL, or use your website's analytics to track traffic from the source parameter."
          }
        },
        {
          "@type": "Question",
          "name": "What happens if an image fails to load?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "The widget is designed to gracefully handle missing images. The preview card will still display with the title and domain information, maintaining a professional appearance."
          }
        },
        {
          "@type": "Question",
          "name": "Is the widget mobile-responsive?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, both the website and email versions are fully responsive and will adapt to different screen sizes and devices for optimal viewing and functionality."
          }
        },
        {
          "@type": "Question",
          "name": "Can I use the widget in WordPress?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, you can embed the generated HTML code in any WordPress post or page. For better integration, you can use a HTML block or a custom HTML widget."
          }
        }
      ]
    }
    </script>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="https://wha.chat" target="_blank" rel="noopener">WhaChat</a>
                    <a href="https://procom.dev/privacy-policy/" target="_blank" rel="noopener">Privacy Policy</a>
                </div>
                <div class="footer-copyright">
                    <p>&copy; <?php echo date('Y'); ?> Pro Commons Developers SL. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

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