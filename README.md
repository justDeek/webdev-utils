# webdev-utils
Various utilities and helper functions for any kind of web-project.

Meant to serve as a library for all kinds of useful features, where only the needed logic for the current project should be cherry-picked to keep the code-base lean and performant.

# Overview

## js

- ### /browser
  - **browser.js** - Functions regarding browser-specific actions like reading/manipulating the query parameters;
  - **clipboard.js** - Copies an input value to the clipboard of the OS;
  - **debug.js** - Simplified and extended versions of common debug functions;

- ### /core
  - **arrays.js** - Helper functions that extend the array prototype;
  - **logic.js** - Basic functions for validating variable values or other general actions. Should be included in most other scripts that depend on these functions;
  - **math.js** - Helpers for math based actions;
  - **strings.js** - Helpers for string operations;
  - **time.js** - Date&Time utilities;

- ### /features
  - **dictionary.js** - Class for creating a dictionary for efficient lookup of all previously provided words.
  - **eyedropper.js** - Initialize the browser-native eyedropper feature to pick the HEX-code for any color on the screen;
  - **suggestion.js** - A class for creating inline-suggestions next to the current element by trying to match the current word with any element from the provided array;
  - **swipe-detect.js** - Utility for detecting swipe-events;

- ### /file-system
  - **files.js** - Helpers for local files;
  - **nodefs-api.js** - A small API for simplifying the interaction with NodeFS;

- ### /html
  - **dom.js** - Shorthands for common dom-related functions;
  - **exdom.js** - A simplified and modernized alternative to jQuery based on cash-dom;
  - **forms.js** - Form-validation utilities as well as helpers for gui controls like inputs, selects, ...;


## php

- ### /_demo

  - **PHPMailer** - A copy of the PHPMailer plugin for sending mails with the sendMail() function defined in main.php;
  - **contact.php** - An example script showcasing a basic contact form utilizing the PHP template functions to generate the HTML and demonstrating the use of the attribute shortcodes. Also contains input checks and sends a mail after
submission;
  - **website.php** - An example website generated via the html.php template syntax;

- ### /core
  - **arrays.php** - Helpers for arrays;
  - **enums.php** - Base class for a custom enumerator implementation in php with an example for a language switcher;
  - **strings.php** - Helpers for string manipulation;

- ### /db
  - **mysqli.php** - Functions that simplify the connection to a MySQL database as well as helpers for
    quickly running SELECT, INSERT, UPDATE or DELETE queries on it;


- **browser.php** - Helpers for browser-specific actions;
- **debug.php** - Useful debugging functions that allow to quickly output multiple variable in a formatted manner;
- **gui.php** - Extensive helpers for creating gui controls like inputs, selects, etc;
- **html.php** - A minimal html template framework for quickly and concisely writing HTML tags with PHP functions. Also converts the passed shortcodes to their full attributes;
- **main.php** - General functions to be used in all other scripts;
- **plugins.php** - Functions for optional plugins;


## styles

- ### /icons
  - **external-link.svg** - The icon to show next to external links;


- **\_forms.scss** - Form-related styles;
- **\_fx.scss** - Styles for optional special effects;
- **\_lists.scss** - List-related styles;
- **\_mixing.scss** - Common sass-functions to prevent code-duplication;
- **\_typography.scss** - Text-related styles;
- **\_utilities.scss** - Utility styles;
- **\_vars.scss** - Global variables;
- **globals.scss** - Global styles and the entry point for the main stylesheet;
