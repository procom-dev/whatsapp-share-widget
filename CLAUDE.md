# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WhatsApp Share Widget Generator - a PHP web application that creates embeddable WhatsApp sharing widgets for websites and emails. Users can input a URL, fetch its metadata, customize the message and appearance, then generate HTML code for both web and email use.

## Architecture

- **Frontend**: jQuery-based single-page application with form validation and live preview
- **Backend**: PHP script for metadata extraction using Open Graph/Twitter Card tags
- **No build process**: Direct file serving with CDN dependencies

### Core Components

- `index.php` - Main application interface with form, preview, and code generation
- `fetch-metadata.php` - Server-side metadata extraction from URLs using DOM parsing
- `assets/js/script.js` - Client-side logic for form handling, AJAX, and widget generation
- `assets/css/style.css` - Complete styling including WhatsApp-styled preview components

### Key Features

- URL metadata fetching with fallback hierarchy (OG → Twitter → Standard meta tags)
- WhatsApp text formatting support (bold, italic, strikethrough, underline)
- Live preview with WhatsApp-styled bubble design
- Dual HTML generation: web widgets and email-compatible tables
- Character limit enforcement (2048 chars for WhatsApp)
- Responsive design with mobile-first approach

## Development Notes

### Dependencies
- jQuery 3.7.1 (CDN)
- Lucide icons (CDN)
- No package managers or build tools

### Key Technical Details

- Widget generation uses inline styles for maximum email client compatibility
- WhatsApp formatting converts `*bold*`, `_italic_`, `~strikethrough~` to HTML
- Image aspect ratio locked to 52.36% for consistent preview appearance
- Share URLs use `wa.me/?text=` format with URL-encoded content
- PHP uses DOMDocument for robust HTML parsing with UTF-8 handling

### File Structure
- Static assets served directly from `assets/` subdirectories
- Error pages (`404.html`, `502.html`) for web server configuration
- No automated testing or linting setup present