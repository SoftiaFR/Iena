# Link to Iframe

This moodle plugin transform resource link to button.
The button then opens a modal with containing a iframe.

Add new GET settings "iframe=true|false", when iframe set to true the footer/header/navbar and block is hiding.

Auteur: Vrignaud Camille *(cvrignaud@softia.fr)*

To install it:
    - deploy in <%%moodle_install%%>/filter
    - Activate filter from "Administration/Plugins/Filters".


How to use it:
    - Filter iframe 

    	Insert a resource  link like this : https://wourmoodle/mod/page/view.php?id=3 
			In your resource.
			
    - Iframe=true/false
     Add this : "&iframe=true" in your URL.
		 Exemple  : https://wourmoodle/mod/page/view.php?id=3&iframe=true
		 
Parameters:

	   The parameters is about button color, is set by default at : #009487
		You can change this parametre with any hex colors.
		
		
> **Additional informations:** The plugin was tested in moodle 3.3 and 3.4
> For more information contact : support_git@softia.fr