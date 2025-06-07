# WhatsApp Share Widget Generator

A PHP web application that creates embeddable WhatsApp sharing widgets for websites and emails. Users can input a URL, fetch its metadata, customize the message and appearance, then generate HTML code for both web and email use.

## Features

- **URL Metadata Fetching**: Automatically extracts title, description, and images from any URL
- **WhatsApp Text Formatting**: Support for bold, italic, strikethrough, and underline formatting
- **Live Preview**: Real-time preview with WhatsApp-styled bubble design
- **Dual HTML Generation**: Creates both web widgets and email-compatible table layouts
- **Character Limit Enforcement**: Respects WhatsApp's 2048 character limit
- **Responsive Design**: Mobile-first approach with clean, modern UI
- **Multi-language Support**: Automatic button text translation based on detected language
- **Analytics Tracking**: Optional UTM parameter injection for tracking

## Tech Stack

- **Frontend**: jQuery 3.7.1, Lucide Icons, Custom CSS
- **Backend**: PHP with DOM parsing for metadata extraction
- **No Build Process**: Direct file serving with CDN dependencies

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/whatsapp-share-widget-generator.git
cd whatsapp-share-widget-generator
```

2. Configure your web server to point to the project directory

3. Ensure PHP is enabled with the following extensions:
   - `libxml`
   - `dom`
   - `mbstring`

4. The application is ready to use!

## Usage

1. **Enter URL**: Input the webpage URL you want to share
2. **Fetch Metadata**: Click "Fetch data" to automatically extract page information
3. **Customize Message**: Edit the WhatsApp message text with formatting support
4. **Configure Options**: Set button text, placement, and tracking preferences
5. **Generate Code**: Copy either web or email HTML code
6. **Embed**: Paste the generated code into your website or email template

## File Structure

```
├── index.php              # Main application interface
├── fetch-metadata.php     # Server-side metadata extraction
├── assets/
│   ├── css/
│   │   └── style.css      # Complete styling
│   ├── js/
│   │   └── script.js      # Client-side logic
│   └── img/
│       ├── whatsapp-logo.png
│       └── favico.png
├── CLAUDE.md              # Development documentation
├── 404.html               # Error page
├── 502.html               # Error page
└── README.md
```

## Security Features

- CSRF protection for all form submissions
- Rate limiting (2-second intervals between requests)
- SSRF protection with URL validation
- Input sanitization and validation
- Secure headers implementation

## Browser Support

- Modern browsers with ES6+ support
- Fallback clipboard functionality for older browsers
- Responsive design for mobile devices

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Acknowledgments

- WhatsApp for the messaging platform and design inspiration
- Lucide for the beautiful icon set
- jQuery for DOM manipulation utilities