# Parameters and Following students  
  
This moodle plugin is the next part of the Sprint 2 "block".  
  
  
Authors :   
  
 - Vrignaud Camille *(cvrignaud@softia.fr)*  
 - Lebeau Michaël *(mlebeau@softia.fr)*  
  
To install it:  
    - Trough the Moodle Administration section "Plugin" -> Add plugin  
    - Activate filter from "Administration/Plugins/Filters".  
  
  
Functionnalities :   
  
 - Progress bar  
        
      The item at the top left of the section shows the percentage of progression of the section. This is the percentage of activities or resources that are in the section and for which activity completion is enabled.  
        
      If activity completion is disabled in the course parameters, or there is no activity or resource in the section under consideration, then this item is not displayed.  
        
      The percentage is truncated to the lower integer.  
        
      In view of the last exchanges, we have added an extra box to have an overall percentage differentiated by the percentage of each activity set (or session / sequence).  
        
       
 - Tracking indicator  
        
        
 This indicator makes it possible to visualize, for a teacher, all the follow-up of the activities of a session defined via a drop-down list. We differed slightly compared to the initial model to keep a coherence between UI and UX, the model had the disadvantage of having to scroll through the columns with arrows to click, which is forbidding. We have chosen to scroll horizontally and vertically with the dataTable.js library, which dynamically manages the data and allows us to add features such as fixed height for the scroll and automatic pagination.  
        

        
 - Indicator present in the section header  
        
Orange indicator of the number of students attending a session  
        
 - Tracking indicator settings page  
 - Section parameter  
 - View of a resource
        
 No apparent change in relation to the basic moodle theme following the internal and external returns, the tooltips are irrelevant and a navigation is already integrated footer. It can be stylized if necessary but it is the maximum on this element.  
     
         
> \*\*Additional informations:\*\* The plugin was tested in moodle 3.3 and 3.4  
> For more information contact : support_git@softia.fr
