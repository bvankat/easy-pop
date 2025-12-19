# Easy Pop - Perfect Popups for WordPress 🍿

A powerful modal and popup builder that integrates seamlessly with the WordPress block editor, allowing you to create stunning modals using any WordPress blocks.

## Key Features

### ✨ It's Native Wordpress!
- **Block Editor** - Design popups using any blocks
- **Lightweight & Fast** - Minimal JavaScript and CSS footprint
- **Accessible** - Full keyboard navigation and focus management
- **Smooth Animations** - Beautiful fade-in effects
- Full visual editing experience
- **Custom post type** - Manage all your popups in one place

### 🎯 Flexible Triggers
- **On Page Load** - Show right away
- **Time Delay** - Wait a few seconds
- **On Scroll** - Trigger at specific scroll percentage
- **On Click** - Open when clicking specific elements (via CSS selector)
- **Exit Intent** - Catch users before they leave

### 📊 Intelligent Display Rules
- **Page Views Tracking** - Show after X page views
- **Session Tracking** - Display after X sessions
- **Frequency Capping** - Limit displays (e.g., "6 times per month")
- **User Targeting** - Show to logged in/out users
- **Device Targeting** - Desktop, mobile, tablet specific
- **Browser Detection** - Target specific browsers
- **URL Referrer** - Show based on where users came from
- **Page/Post Selector** - Sensibile defaults *and* fine-tuned control


### 🎨 Display Options
- Multiple position options (center, corners, etc.)
- Animation effects (fade, slide, zoom)
- Customizable overlay
- Close button control
- ESC key support

### Accessibility
* Focus trapping within modal
* Keyboard navigation (ESC to close, TAB to navigate)
* ARIA labels and roles
* Focus restoration on close
* Screen reader friendly

### Performance
* JavaScript: ~2.8KB minified
* CSS: ~1.2KB minified
* No external dependencies
* Efficient event handling with debouncing
* Cookie-based frequency control


## Usage

### Creating Your First Popup

1. Go to **Popups** → **Add New** in your WordPress admin
2. Give your popup a title
3. Design your popup content using any Gutenberg blocks
4. Configure display settings in the right sidebar:
   - **Conditions** - Choose where to display
   - **Triggers** - Set when to show the popup
   - **Advanced Rules** - Add targeting and frequency rules
5. Publish your popup

### Use Cases ###

- Newsletter signups
- Special offers and promotions
- Important announcements
- Lead generation forms
- Video presentations
- Survey and feedback collection
- Cookie consent notices
- Age verification gates
- Exit intent offers

### Example Scenarios

#### Newsletter Signup (Exit Intent)
- **Trigger**: Exit Intent
- **Advanced**: Show to logged out users only
- **Frequency**: Maximum 1 time per week

#### Special Offer Announcement
- **Trigger**: On Page Load (3 second delay)
- **Conditions**: Show on product pages
- **Schedule**: Campaign start/end dates
- **Frequency**: 3 times per month

#### Content Upgrade
- **Trigger**: On Scroll (70% down)
- **Conditions**: Show on blog posts
- **Advanced**: After 2 page views

#### Mobile App Promotion
- **Trigger**: On Page Load
- **Device**: Mobile only
- **Frequency**: 2 times per month


## Settings Reference

### Triggers

* **Page Load** - Modal appears immediately when page loads
* **Time Delay** - Modal appears after X seconds
* **Scroll Percentage** - Modal appears when user scrolls to X% of page
* **Click** - Modal appears when element matching CSS selector is clicked
* **Exit Intent** - Modal appears when user moves cursor toward browser close button

### Display Rules

### Display Options


## Technical Details


### Requirements
- WordPress 6.0 or higher
- PHP 7.4 or higher
- JavaScript enabled in browser


## Customization

### CSS Classes
### Filters
### Actions


## Frequently Asked Questions

= Can I use any WordPress blocks in my modals? =

Yes! The modal content area supports all WordPress blocks, including third-party blocks from other plugins.

= How does the frequency control work? =

The plugin uses cookies to track when a modal has been shown. Based on your settings, it will respect the display frequency you've chosen.

= Can I have multiple modals on the same page? =

Yes, you can create as many modals as you need. Each modal's trigger conditions are evaluated independently.

= Is the modal accessible? =

Absolutely! The modal includes proper ARIA labels, focus trapping, keyboard navigation (ESC to close), and focus restoration when closed.

= Does it work on mobile devices? =

Yes! The modal is fully responsive and you can even choose to show modals only on specific device types.

= How do I trigger a modal on button click? =

Set the trigger to "Click" and provide a CSS selector (like ".open-modal-button" or "#special-offer-btn"). Any element matching that selector will open the modal when clicked.

= Can I customize the modal styling? =

Yes! The modal uses minimal base styles, and you can add custom CSS to match your site's design. The modal content inherits your theme's block styles.

= Does it impact site performance? =

No! The plugin is optimized for performance with minimal JavaScript (under 3KB) and CSS. Modals only load the resources they need.


## Screenshots — TK

1. Modal post type management screen
2. Block editor interface for designing modal content
3. Modal display settings panel
4. Frontend modal with smooth animations
5. Mobile responsive modal view


## Privacy & GDPR — TK

## Changelog

### Version 0.5.1
* Fixes exclusion save bug
* Adds default options for exclusions - Homepage only, Pages only, Posts only, Selected pages/posts


### Version 0.5.0
* Adds exclusion option for better page/post targeting

### Version 0.4.1
* Adds full-width option
* Removes background styling on modal container
* Updates most code references from 'modal-builder' to 'easy-pop'

### Version 0.3.0
* Adds post/page targeting controls

### Version 0.2.1
* Working version
* Renames plugin

### Version 0.1.0
* Pre-release
* Custom post type for modals
* Block editor integration
* Multiple trigger options
* Frequency control
* Device targeting
* Accessibility features
* Smooth animations


## Credits

Created by Ben Vankat, Hanscom Park Studio with an assist from Wordpress Telex
