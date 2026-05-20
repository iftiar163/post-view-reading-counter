# Post View Counter

A lightweight WordPress plugin that tracks post views and accumulates visitor reading time. Display comprehensive engagement metrics in your admin dashboard and post list columns.

## Features

- **📊 View Tracking**: Automatically track the number of visitors viewing each post
- **⏱️ Reading Time Tracking**: Measure and accumulate the actual time visitors spend reading posts
- **📈 Admin Dashboard Widget**: Beautiful analytics widget showing total posts, views, and cumulative reading time
- **📋 Post Columns**: Display views and reading time directly in the post list table
- **🎯 Smart Filtering**: Excludes administrators and editors from view counts (tracks real visitors only)
- **⚡ Lightweight**: Minimal database impact with efficient AJAX tracking
- **🔒 Secure**: Built with WordPress security standards (nonce verification, sanitization, proper escaping)
- **📱 Responsive**: Dashboard widget adapts beautifully to all screen sizes

## How It Works

### View Tracking
- JavaScript tracks when a visitor loads a post (after 2-second bounce filter)
- AJAX sends view count to the server
- Count is stored in post meta and incremented
- Prevents double-counting in the same session using sessionStorage

### Reading Time Tracking
- JavaScript measures actual time spent on the post
- Records time when visitor leaves the page or switches tabs
- Requires minimum 3 seconds (filters bounces)
- Caps at 30 minutes (prevents outliers)
- Time is accumulated across all visitors and stored per post
- Dashboard shows total reading time across all posts

## Dashboard Widget

The admin dashboard includes a beautiful analytics widget displaying:
- **Published Posts**: Total number of published posts
- **Total Views**: Sum of all views across all posts
- **Total Read Time**: Cumulative reading time from all visitors across all posts (shown as "X hrs Y mins")

## Post List Columns

Two new columns appear in the post list table:
- **👁️ Views**: Number of views for each post
- **⏱️ Read Time**: Total accumulated reading time for each post (clickable to sort)

## Installation

1. Download the plugin files
2. Upload to `/wp-content/plugins/post-view-counter/`
3. Activate in WordPress admin under Plugins
4. Visit the dashboard to see the new analytics widget
5. Go to Posts to see the new columns

## Usage

Once activated, the plugin works automatically:
- New posts will start tracking views and reading time immediately
- Visit a post from the frontend to trigger tracking
- Check the admin dashboard for real-time engagement metrics
- Sort posts by views or reading time in the post list

## Security Features

- ✅ Nonce verification on all AJAX requests
- ✅ Input sanitization and validation
- ✅ Proper output escaping
- ✅ User role filtering (admins/editors excluded)
- ✅ Post status verification (only published posts tracked)

## Compatibility

- **Minimum WordPress**: 5.0
- **Minimum PHP**: 7.4
- **Browser Support**: All modern browsers (Chrome, Firefox, Safari, Edge)

## Database

The plugin stores data in post meta using these keys:
- `_pvc_view_count`: Total view count for a post
- `_pvc_reading_time`: Total reading time in seconds for a post

No custom tables are created. Data is stored using WordPress standard post meta.

## Performance

- Uses efficient AJAX requests to minimize server load
- SessionStorage prevents duplicate counting in same session
- Database queries use proper prepared statements
- No significant impact on site performance

## Uninstallation

Simply deactivate and delete the plugin. All plugin data is automatically cleaned up from the database.

## FAQ

**Q: Will this track admin views?**
A: No. By default, administrators, editors, authors, and contributors are excluded from view counts. Only real visitors are tracked.

**Q: How accurate is the reading time?**
A: It measures actual time spent on the page. It starts after a 2-second delay (to filter bounces) and stops when they leave or switch tabs. Times are capped at 30 minutes to filter outliers.

**Q: Does this slow down my site?**
A: No. The tracking uses efficient AJAX requests and minimal JavaScript. The impact is negligible.

**Q: What happens if a user blocks JavaScript?**
A: Views won't be tracked for that user. Reading time also requires JavaScript. This is standard for most analytics plugins.

**Q: Can I reset the counts?**
A: You can manually delete post meta using database tools or a custom SQL query. The plugin doesn't include a reset feature.

## Support

For issues, questions, or feature requests, please use the WordPress plugin forum or contact the author.

## License

This plugin is licensed under GPL-2.0-or-later. You are free to use, modify, and distribute it under the terms of the GNU General Public License.

## Changelog

### Version 1.0.0
- Initial release
- View tracking functionality
- Reading time accumulation
- Dashboard widget with analytics
- Post list columns for engagement metrics

---

Made with ❤️ for WordPress developers
