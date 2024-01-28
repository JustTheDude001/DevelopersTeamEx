Okay... I think I am not doing it correctly...
I used the layout file to add the scripts and the stylesheet which I compiled using Tailwindcss.
Nonetheless would it be better to have a common layout an furthermore a layout for each "route"?
And in scripts just write the php scripts used in each different layout for each route?
I think it would be cleaner, now I need to look for somewhere to add the php function which I use in the "layouts" defined in the scripts folder...
I am sure I a missing something important...
Okay some notes:
	- Controller -> controlls the web and links view and model
	- Model -> Controls the persistency (JSON/Database..) It is used by the controller to retrieve the data which is given 
	afterwards to the view.
	- View -> Shows the data given from the controller in an specific way.
	
Therefore:
	TODO:
		- Check View files again
		- Check how to bind view and model for listing the tasks
		
