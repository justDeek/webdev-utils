# webdev-helpers
Various helper functions for PHP and JS for any kind of project.

## TOC

### examples/
**contact.php** - An example script showcasing a basic contact form utilizing the PHP template functions to generate the HTML and demonstrating the use of the attribute shortcodes. Also contains input checks and sends a mail after 
submission.

### js/
**arrays.js** - Helper functions that extend the array prototype.\
**browser.js** - Functions regarding browser-specific actions like reading/manipulating the query parameters.\
**clipboard.js** - Copies an input value to the clipboard of the OS.\
**controls.js** - Helpers for gui controls like inputs, selects, ...\
**debug.js** - Simplified/extended versions of common debug functions\
**files.js** - Helpers for local files.\
**general.js** - Basic functions for validating variable values or other general actions. Should be included in most other scripts that depend on these functions.\
**math.js** - Helpers for math based actions.\
**strings.js** - Helpers for string operations.

### php/
#### db/
**mysqli.php** - Functions that simplify the connection to a mysqli database as well as containing helpers for 
quickly running SELECT, INSERT, UPDATE or DELETE queries on it.
#### PHPMailer/
A copy of the PHPMailer plugin for sending mails with the sendMail() function defined in main.php\
\
__---__\
**arrays.php** - Helpers for arrays.\
**browser.php** - Helpers for browser-specific actions.\
**debug.php** - Useful debugging functions that allow to quickly output multiple variable in a formatted manner.\
**enums.php** - Base class for a custom enumerator implementation in php with an example for a language switcher.\
**gui.php** - Extensive helpers for creating gui controls like inputs, selects, etc.\
**html.php** - A minimal html template framework for quickly and concisely writing HTML tags with PHP functions. Also converts the passed shortcodes to their full attributes.\
**main.php** - General functions to be used in all other scripts.\
**plugins.php** - Functions for optional plugins.\
**strings.php** - Helpers for String manipulation.
