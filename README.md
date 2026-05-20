=== Post View Counter ===
Contributors: iftiarhossain
Tags: post views, reading time, analytics, engagement, tracking
Requires at least: 5.0
Requires PHP: 7.4
Tested up to: 6.9
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0-html

Track post views and accumulate total reading time from visitors. Display engagement metrics in your admin dashboard and post list columns.

== Description ==

Post View Counter is a lightweight, efficient WordPress plugin that automatically tracks how many times each post is viewed and measures the total reading time accumulated from all visitors. Perfect for bloggers, content creators, and site owners who want to understand their audience engagement at a glance.

**Key Features:**

* **View Tracking** - Automatically count the number of unique sessions viewing each post with intelligent bounce filtering
* **Reading Time Analytics** - Measure actual time visitors spend reading your posts
* **Admin Dashboard Widget** - Beautiful analytics widget showing total views, posts, and cumulative reading time
* **Post List Columns** - Display views and reading time directly in the post list for quick reference
* **Smart User Filtering** - Automatically excludes administrators and editors from view counts to track real visitor engagement
* **Session-based Counting** - Prevents artificially inflating counts by tracking views per session, not per page load
* **Security First** - Built with WordPress security standards including nonce verification, input sanitization, and proper output escaping
* **Lightweight & Efficient** - Minimal database footprint with optimized AJAX requests and prepared statements
* **Zero Configuration** - Works automatically after activation, no settings to configure
* **Responsive Design** - Dashboard widget looks perfect on all devices and screen sizes

== Installation ==

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Search for "Post View Counter"
4. Click **Install Now** and then **Activate**
5. Visit your **Dashboard** to see the new analytics widget
6. Go to **Posts** to see the new Views and Read Time columns

**Manual Installation:**

1. Download the plugin from WordPress.org
2. Upload the `post-view-counter` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress
4. The plugin will start tracking immediately

== Frequently Asked Questions ==

= Does this plugin track admin views? =

No. By design, administrators, editors, authors, and contributors are excluded from view counts. The plugin tracks only real visitor views, giving you accurate engagement metrics.

= How accurate is the reading time? =

Reading time is measured from actual visitor activity on your posts. It begins after a 2-second initial delay (to filter out bounces) and stops when visitors leave the page or switch tabs. Times are capped at 30 minutes to exclude outliers.

= Will this slow down my site? =

No. The plugin uses efficient AJAX requests and minimal database queries. All tracking is done asynchronously, and there's no noticeable impact on site performance.

= How is the data stored? =

All data is stored using WordPress standard post meta. No custom database tables are created. This keeps your database clean and easy to manage.

= Can I reset the view counts? =

Currently, view and reading time data cannot be reset through the plugin interface. However, you can manually delete the data through phpMyAdmin or by removing the post meta keys `_pvc_view_count` and `_pvc_reading_time`.

= What browsers are supported? =

The plugin works on all modern browsers including Chrome, Firefox, Safari, Edge, and Opera. It requires JavaScript to be enabled for tracking to work.

= Does this plugin use cookies? =

The plugin uses browser sessionStorage (not cookies) to prevent double-counting views from the same visitor session. SessionStorage is cleared when the browser closes.

= Is my visitor data private? =

Yes. All data is stored locally in your WordPress database. No information is sent to external servers or third parties.

== How It Works ==

**View Tracking Process:**
1. When a visitor loads a post, JavaScript on the page waits 2 seconds (bounce filter) before sending a tracking request
2. An AJAX request is sent to your WordPress backend
3. The view count for that post is incremented in the post meta
4. SessionStorage tracks that this post has been counted in the current session to prevent duplicates

**Reading Time Tracking Process:**
1. JavaScript monitors how long a visitor spends on the page
2. Timing begins after the initial 2-second bounce delay
3. Timing ends when the visitor leaves the page or switches browser tabs
4. Only intervals of 3+ seconds are counted (minimum threshold)
5. Maximum 30-minute intervals are recorded (filters outliers like tabs left open)
6. The accumulated time is added to the post's total reading time meta

**Dashboard Widget:**
The admin dashboard displays:
- Total number of published posts
- Sum of all views across all posts
- Cumulative reading time from all visitors (formatted as "X hours Y minutes")

**Post List Columns:**
Two new columns in the Posts list show:
- Views: Total view count for each post
- Read Time: Total accumulated reading time (click to sort posts)

== Technical Details ==

**Database:**
- `_pvc_view_count` - Stores the total view count for a post
- `_pvc_reading_time` - Stores total reading time in seconds for a post
- Uses standard WordPress post meta (no custom tables)

**Security:**
- All AJAX endpoints verify nonces for CSRF protection
- User input is sanitized and validated
- Output is properly escaped for display
- User roles are verified to exclude administrators and editors
- Only published posts are tracked

**Performance:**
- Efficient prepared statements for all database queries
- Asynchronous AJAX requests don't block user interaction
- SessionStorage prevents redundant database calls
- Minimal CSS and JavaScript payload
- No external API calls or third-party services

== Changelog ==

= 1.0.0 =
* Initial release
* View tracking with bounce filtering
* Reading time analytics with min/max thresholds
* Admin dashboard widget with engagement metrics
* Post list columns for views and reading time
* Smart user role filtering
* Security-first implementation with nonce verification
* Comprehensive documentation and FAQ

== Support ==

For support, feature requests, or bug reports, please visit the plugin's WordPress.org support forum. Make sure to:

1. Describe your issue clearly
2. List your WordPress version and PHP version
3. Let us know which plugins are active
4. Share any error messages from your browser console (press F12)

== Credits ==

Developed by Iftiar Hossain

== License ==

This plugin is licensed under the GPL-2.0-or-later license. You are free to use, modify, and distribute this plugin under the terms of the GNU General Public License.

For more information about the GPL, visit: https://www.gnu.org/licenses/gpl-2.0-html

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
