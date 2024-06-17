Gearshift is intended as a more admin configurable theme.  While it maintains the basic layout of the core theme, colors, fonts, and site graphics are pulled into a configuration file.

To set the site look and feel, make a copy of '\gearshift\config.php.default', and name it '\gearshift\config.php'.  From there, change the values associated with the color variables to any standard text or hexcode value:

https://htmlcolorcodes.com/

For graphics (site logo and browser icon), drop the appropriate files into the gearshift folder, and then change the names of the values shown for "$gear_shift_icon" and "$gear_shift_logo".  

Finally, for font families, use any OS supported fonts using the CSS font-family syntax:

https://www.w3schools.com/cssref/pr_font_font-family.asp 

"config.json" must exist but contains optional configuration.

menuleftonly: module names included in this array will always display the full left-side menu, regardless of screen width.
menutoponly: module names included in this array will always use the roll-up top-left corner menu, regardless of screen width.