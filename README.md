# Form Conditioner #

This plugin allows you to to add client side conditional logic to any form using javascript. Check out a video demo here:

https://youtu.be/B0sy09ooZ2A

All you need is to. . 

1.) Install the plugin zip file normally as you any plugin. Once installed successfully, go the the plugin's set up page which can be found at:
Site Administration > Plugins > Local Plugins > Form Conditioner
. . . or otherwsie: http://{your site}.com/local/form_conditioner/index.php

2.) Find a form page's url (Note: there is no support for Cross-Origin request currently. Please only use links on the same domaain that the plugin is installed on. . . although, theoretically, this could be used to work on any HTML form not just moodle forms, but you'd have ot use it offline or use it on sites that support cross domain request). 

3.) Once you have found the form's url, select the form from the page (there could be serveral), then 

4.) Add the conditional rule sets. They are based on values on HTML name elements in any HTML form. As many rules as you want. It is up to you to figure out the appropriate name selector on the form. Use you browser inspector or look at the preview code you see when you selected the form.

5.) Make sure all of your conditions are set then hit "Generate Conditions". You should be presented with the JS required to add that conditional logic to your forms. You can either copy the code, or download a JS file with the script. From there you have to upload the JS to the site. Their are a number of ways to do this: a.) Some themes have support for adding your own JS in their theme settings b.) manually add it sitewide (not recommended) c.) Add a "javascript" folder to your theme and drop the JS file into it d.) if the form exist on another plugin, you may be able to add the JS just to that plugin, etc. . . There are a number of different ways. I think maybe even a moodle plugin that will allow it. Just good "adding js to moodle" and pick a solution that's right for you. I'm just helping you to not have to write the code from scratch

Note: Moodle's Forms API does have support for condiitonal logic, and that probably the proper way to handle conditional logic in forms, but if you're not a developer, or just had some simple logic and didn't want to write anything from scratch, maybe this will help. 

Cheers!



## License ##

2021 Paul <paul-edoho-eket@uiowa.edu>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
