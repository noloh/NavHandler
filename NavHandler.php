<?php
class NavHandler extends Object
{
	private $ActiveSection;
	private $Launched;
	private $ContentPanel;
	private $Token;
	private $CreationFunction;
	
	static $Section, $Width, $Height;
	
	function NavHandler($contentPanel, $creationFunction, $group = null, $tokenName='section')
	{
		$this->ContentPanel = $contentPanel;
		$this->Token = $tokenName;
		$this->CreationFunction = $creationFunction;
		
		if($this->Token)
		{
			$token = URL::GetToken($this->Token);
			if($token)
			{
				if($group && $group instanceof Group)
					$group->SelectedValue = $token;
				$this->LaunchSection($token);
			}
		}
	}
	function LaunchSection($section = null, $reCreate = false)
	{
		if($section instanceof Group)
			$section = $section->SelectedValue;
	
		$parent = $this->ContentPanel;
		
		if($this->Launched[$section] == null || $reCreate)
		{
			self::$Section = $section;
			self::$Width = $parent->Width;
			self::$Height = $parent->Height;
			
			$object = $this->CreationFunction->Exec();
			if($object)
			{
				$this->Launched[$section] = $object;
				$this->ContentPanel->Controls->Add($this->Launched[$section]);
			}
			else
				return;
		}
		if($this->ActiveSection != null)
			$this->Launched[$this->ActiveSection]->Visible = System::Vacuous;
		if($this->Token)	
			URL::SetToken($this->Token, $section);
		$this->ActiveSection = $section;
		$this->Launched[$section]->Visible = true;
	}
	function GetContentPanel()	{return $this->ContentPanel;}
	function GetActiveSection()	{return $this->ActiveSection;}
}
?>