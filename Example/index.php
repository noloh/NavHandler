<?php
//Path to NOLOH on your system, this is our test path.
require_once('/var/www/htdocs/Stable/NOLOH/NOLOH.php');
/*
 * Path to NavHandler Nodule, only necessary if the NavHandler
 * nodule is not placed in the NOLOH nodules folder.
 */
require_once('../NavHandler.php');

class NavHandlerTest extends WebPage
{
	private $Nav1;
	
	function NavHandlerTest()
	{
		parent::WebPage('Welcome to NavHandler Test');
		$this->BackColor = '#999999';
		/*Group for groupable items, used in this example as the
		  navigation triggers*/
		$group = new Group();
		//RolloverLabels to be used as navigation elements
		$rollover1 = new RolloverLabel('red');
		$rollover2 = new RolloverLabel('green');
		$rollover3 = new RolloverLabel('blue');
		//Set Layout to Relative for our Rollovers for lazy alignment
		$rollover1->Layout = $rollover2->Layout = $rollover3->Layout = Layout::Relative;
		//Add RolloverLabels to our group
		$group->AddRange($rollover1, $rollover2, $rollover3);
		/*Our content panel which will be used to display the various
		  sections triggered by the navigation elements and NavHandler*/
		$contentPanel = new Panel(10, 100, 500, 500);
		$contentPanel->Border = 1;
		/* Our NavHandler object, note that this must be stored
		 * in a variable of some object to ensure it does not get
		 * cleaned up by garbage collection
		 * 
		 * The first parameter of the constructor takes in a ContentPanel
		 * where your sections will display.
		 * 
		 * The second parameter takes in an event which will be called to
		 * create a section.
		 * 
		 * The optional third parameter "group" takes in a Group object to be used in conjuction
		 * with the NavHandlers token for BookmarkFriendly.
		 * 
		 * The optional fourth parameter, not listed in the line below
		 * allows for a token name, the default is 'section' to be used to
		 * generate a url token in conjuction with bookmark friendly. If set
		 * to false, then the NavHandler will not create tokens or be bookmarkable. 
		 * */
		$this->Nav1 = new NavHandler($contentPanel, new ServerEvent($this, 'CreateSection'), $group);
		/*We now set the Select of each of our RolloverLabels to trigger the NavHandler
		  LaunchSection function. LaunchSection takes in the section as the first parameter
		  and also has an optional second parameter reCreate, not used here which will always
		  call your NavHandler function even if the previous object associated with that section
		  is already in use. Also note that if a group is passed in the SelectedValue of
		  the group will be used.*/
		$rollover1->Select = new ServerEvent($this->Nav1, 'LaunchSection', 'red');
		$rollover2->Select = new ServerEvent($this->Nav1, 'LaunchSection', 'green');
		$rollover3->Select = new ServerEvent($this->Nav1, 'LaunchSection', 'blue');
		/*Alternative use of NavHandler, using our group. Since the section would now correspond
		  to the SelectedValue of the Group.*/
		//$group->Change = new ServerEvent($this->Nav1, 'LaunchSection', $group);
		
		//Add the group, and contentPanel to the Controls of the WebPage
		$this->Controls->AddRange($group, $contentPanel);
	}
	/**
	 * This is our Event Handler for creation of sections, note that it must return an object
	 * to be used in conjuction with the NavHandler 
	 */
	function CreateSection()
	{
		/*To access the section that's being created, we need to access the Section
		  variable of the NavHandler class*/
		$section = NavHandler::$Section;
		/* We can also access the Width, and Height of the ContentPanel which will
		 * be displaying our section.*/
		$width = NavHandler::$Width;
		$height = NavHandler::$Height;
		
		/*The following shows an example of how you would handle the section and decide
		  what section to create. In the following example, ideally you would create one
		  panel, then set the BackColor or Location to from the variable, but for clarity
		  each case is separated out, as would be in more complex situations*/
		switch($section)
		{
			case 'red':
				$object = new Panel(10, 10, $width - 50, $height - 50);
				$object->BackColor = 'Red';
				break;
			case 'green':
				$object = new Panel(20, 20, $width - 70, $height - 70);
				$object->BackColor = 'Green';
				break;
			case 'blue':
				$object = new Panel(0, 0, $width, $height);
				$object->BackColor = 'Blue';
				break;
		}
		return $object;
	}
}
?>