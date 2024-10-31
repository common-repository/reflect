=== Reflect ===
Contributors: tkriplean
Tags: comments, civility, summary
Requires at least: 2.9.0
Tested up to: 3.2
Stable tag: Trunk

Crowdsourced comment summarization. Helps people listen. Helps everyone find the useful points. 

== Description ==

Crowdsourced comment summarization. Helps people listen. Helps everyone find the useful points. 

Reflect changes your comments section. It adds a space next to every comment where other readers can succinctly restate points they hear the commenter making. This design encourages people to explicitly **listen**, rather than just speaking. Commenters can come back and verify the accuracy of the restatement, and, if necessary, clarify their message. Other readers are then able to read the original comment, interpretations of that comment, and clarifications. Reflect thus creates a richer commenting environment by balancing speaking and listening. 

**Check out the screenshots**. It makes more sense just looking at those :-)

It can sometimes take some elbow grease to get Reflect to work on your blog. Here's why: 

1. Reflect works by wrapping html around certain HTML elements related to your comment section. Reflect assumes the default Wordpress comment structure, but themes frequently overwrite this default structure for their own custom, arbitrary comment structure. For themes that do override the structure, the Reflect plugin allows you to specify the CSS selectors that Reflect needs to operate using the current theme. (directions, screenshots)
1. When you get Reflect to show up, it will often not look that great and custom CSS modifications may be necessary. For example, the default Reflect CSS assumes a light, preferably white, background. But most importantly, a Reflect enabled comment board requires more horizontal space than the default Wordpress comment board. Your comment section should be at least 600 px wide, and you must be willing to allocate reflect at least 250 of those pixels. We recommend a width of 800 pixels for your comment section.

A somewhat verbose **screencast that walks through the install process** and some of the customization steps can be found at http://www.cs.washington.edu/homes/travis/installing_reflect.mp4.

**Demo:** http://abstract.cs.washington.edu/~travis/seattlespeaks/. Feel free to play around with it!

Please contact the plugin author if you want to make it work for your site or if you encounter problems. He may be able to help!

Some notes:

1. This plugin is probably only useful for blogs receiving a decent amount of comments. 
1. The plugin makes a comment board feel cramped if the theme does not allocate enough horizontal space to the comment section.
1. IE6 & 7 are NOT supported
1. This plugin is not yet internationalized.

== Installation ==

1. Download the Reflect directory to `/wp-content/plugins/`
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Make sure to go to settings/Reflect in the admin panel to configure Reflect
1. A screencast about installing and customizing Reflect can be found at http://www.cs.washington.edu/homes/travis/installing_reflect.mp4.

== Screenshots ==

1. Hovering over a bullet point that someone else added.
2. Adding a new bullet point (part 1). Type in the summary, 140 char or less.
3. Adding a new bullet point (part 2). Connect your summary to the place in the commenter's text where they were making the point.
4. Eric is coming back and verifying whether a summarizer accurately portrayed the points he was trying to make. He can also leave a 140 char response to the bullet. 

== Changelog ==

= 0.1 =
* Initial import. Experimental. Difficult to get to work on your site. 

= 0.1.3 = 
* Admin settings panel
* Responses fix

= 0.1.5 = 
* Very minor tweaking to make sentence parsing better and proper rendering of escaped text.

= 0.1.6 = 
* Added email notifications that are sent to a commenter when someone summarizes their comment.

= 0.1.7 = 
* adding sender header to email

= 0.1.8 = 
* minor bug fix

= 0.1.9 =
* fixing bug with notification sender

= 0.2.0 =
* adding the ability to rate bullet summaries
* design update
* fixing issues with db upgrades
* improving generality of Reflect across wordpress installations

== Upgrade Notice ==

= 0.1.3 =
* Admin settings panel where you can set an option that tells Reflect how to work.

= 0.1.4 =
* Misc style updates. You should modify your theme's style.css file in order to customize to the Reflect markup. 

= 0.1.5 = 
* Very minor tweaking to make sentence parsing better and proper rendering of escaped text.

= 0.1.6 = 
* Added email notifications that are sent to a commenter when someone summarizes their comment.

= 0.1.7 = 
* adding sender header to email

= 0.1.8 = 
* minor bug fix

= 0.1.9 =
* fixing bug with notification sender

= 0.2.0 =
* adding the ability to rate bullet summaries
* design update
* fixing issues with db upgrades
* improving generality of Reflect across wordpress installations
